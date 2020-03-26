<?php
session_start();
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('db.db');
    }
}

DEFINE('HTTP_HOST', $_SERVER['REQUEST_SCHEME'] . "://" . "playground.loc");
DEFINE('HTTP_URL', HTTP_HOST . $_SERVER['REQUEST_URI']);

$db = new MyDB();

class Helper {
    private static $db = null;

    public static function init ($db) {
        self::$db = $db;
    }

    public static function dd ($str, $die = true) {
        echo "<pre>";
        print_r($str);
        echo "</pre>";
        if ($die) {
            die();
        }
    }

    public static function redirect ($url) {
        header('location:' . $url);
    } 

    public static function redirectBack (array $input, $index = 'data') {
        $_SESSION[$index] = $input;
        self::redirect($_SERVER['HTTP_REFERER']);
    }

    public static function requireToVar ($file) {
        ob_start();
        require($file);
        return ob_get_clean();
    }

    public static function view ($name, array $data = []) {
        return self::requireToVar('view/' . $name . '.php');
    }

    public static function input () : array {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                return $_POST;
            break;
            case 'GET':
                return $_GET;
            break;
        }
    }

    public static function validate(array $input, array $rules) {
        $errors = [];
        foreach($rules as $rule_input_name => $rules_list) {
            if (isset($input[$rule_input_name])) {
                foreach ($rules_list as $rule_signature => $err_string) {
                    $rule_parsed = explode(":", $rule_signature);//0 name, >0 args
                    switch ($rule_parsed[0]) {
                        case 'required':
                            if (strlen($input[$rule_input_name]) == 0) {
                                $errors[] = $err_string;
                            }
                        break;
                        case "unique":
                            if ($rule_parsed[1]) {
                                $elem = self::$db->querySingle("SELECT * FROM users WHERE email = '" . $input[$rule_input_name] . "';");
                                if ($elem) {
                                    $errors[] = $err_string;
                                }
                            }
                        break;
                    }
                }
            } else {//Data MUST BE always
                throw new Exception('Input ' . $rule_name . ' is undefined');
            }
        }
        return count($errors) ? $errors : true;
    }
}

$request = $_SERVER['REQUEST_URI'];

$routes = [
    [
        'url' => "/",
        'controller' => 'index',
        'method' => 'GET',
        'middleware' => ['isLogged']
    ],
    [
        'url' => '/login',
        'controller' => 'login',
        'middleware' => ['isUnlogged'],
        'method' => 'GET',
    ],
    [
        'url' => '/login',
        'controller' => 'loginPost',
        'method' => 'POST',
    ],
    [
        'url' => '/logout',
        'controller' => 'logout',
    ],
    [
        'url' => '/register',
        'controller' => 'register',
        'middleware' => ['isUnlogged'],
        'method' => 'GET',
    ],
    [
        'url' => '/register',
        'controller' => 'registerPost',
        'middleware' => ['isUnlogged'],
        'method' => 'POST',
    ],
    [
        'url' => '/get-history/{?page}',
        'controller' => 'getHistory',
        'middleware' => ['isLogged']
    ],
    [
        'url' => '/delete-history/{id}',
        'middleware' => ['isLogged'],
        'controller' => 'deleteHistory',
    ],
    [
        'url' => '/make-action/{name}',
        'middleware' => ['isLogged'],
        'controller' => 'makeAction',
    ]
];

class Middleware {
    public static function isLogged () : bool {
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            return true;
        }
        Helper::redirect('/login');
        return false;
    }
    
    public static function isUnlogged () : bool {
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            Helper::redirect('/');
            return false;
        }
        return true;
    }
}

class Controller {
    private static $db = null;

    public static function init($db) {
        self::$db = $db;
    }

    public static function index () {
        return Helper::view('index');
    }

    public static function login () {
        return Helper::view('login');
    }

    public static function loginPost () {
        $input = Helper::input();
        $validation = Helper::validate($input, [
            'login'     => [
                'required' => 'Enter your email!'
            ],
            'password'  => [
                'required' => 'Enter your password!'
            ],
        ]);
        if ($validation === true) {
            $user = self::$db->querySingle("SELECT * FROM users where email='" . $input['login'] . "'", true);
            if (password_verify($input['password'], $user['password'])) {
                $_SESSION['id']         = $user['id'];
                $_SESSION['name']       = $user['name'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['loggedin']   = true;
                unset($_SESSION['errors']);
                Helper::redirect('/');
            } else {
                Helper::redirectBack(['Credentials missmatch'], 'errors');
            }
        } else {
            Helper::redirectBack($validation, 'errors');
        }
    }

    public static function logout () {
        session_destroy();
        Helper::redirect('/login');
    }

    public static function register () {
        return Helper::view('register');
    }

    public static function registerPost () {
        $input = Helper::input();
        $validation = Helper::validate($input, [
            'name'     => [
                'required' => 'Enter your name!'
            ],
            'email' => [//no need to validate email
                'required' => 'Enter your email!',
                'unique:users' => 'Email is already taken!'
            ],
            'password'  => [//on my opinion everybody have rights to be unprotected
                'required' => 'Enter your password!'
            ],
        ]);
        if ($validation === true) {
            $res = self::$db->query("INSERT INTO users 
                (name, email, password) 
                VALUES 
                (
                    '" . $input['name'] . "', 
                    '" . $input['email'] . "', 
                    '" . password_hash($input['password'], PASSWORD_DEFAULT) . "'
                )"
            );
            unset($_SESSION['errors']);
            Helper::redirect("/login");
        } else {
            Helper::redirectBack($validation, 'errors');
        }
    }
    
    public static function not_found () {
        return Helper::view('not_found');
    }

    public static function getHistory (array $data) {
        $page = 1;
        $pageSize = 14;
        if (isset($data['page'])) {
            $page = $data['page'];
        } else {
            Helper::redirect('/get-history/1');
        }
        $input = Helper::input();
        $count = self::$db->querySingle("SELECT count(id) FROM actions");
        $pages = ceil($count/$pageSize);
        if ($page > $pages) {
            Helper::redirect('/get-history/' . $pages);
        } else if ($page < 0) {
            Helper::redirect('/get-history/1');
        }

        $entries = self::$db->query("SELECT act.*, us.name FROM actions act
        INNER JOIN users us ON (us.id = act.user_id)
        ORDER BY id DESC
        LIMIT $pageSize OFFSET " . ($page - 1) * $pageSize);

        $result = [
            'page'      => $page,
            'pageMax'   => $pages,
            'data'      => []
        ];
        while ($row = $entries->fetchArray()) {
            $result['data'][] = [
                'id'            => $row['id'],
                'pokemon_name'  => $row['pokemon_name'],
                'created_at'    => $row['created_at'],
                'user_id'       => $row['user_id'],
                'name'          => $row['name'],
            ];
        }
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public static function deleteHistory (array $data) {
        if (isset($data['id'])) {
            $id = $data['id'];
        } else {
            header('Content-Type: application/json');
            return json_encode(['result' => 'error']);
        }
        self::$db->query("DELETE FROM actions WHERE id=$id");
        header('Content-Type: application/json');
        return json_encode(['result' => 'success']);
    }

    public static function makeAction (array $data) {
        if (isset($data['name'])) {
            $name = $data['name'];
        } else {
            header('Content-Type: application/json');
            return json_encode(['result' => 'error']);
        }
        $created_at = date("Y-m-d H:i:s");
        $res = self::$db->query("INSERT INTO actions 
                (pokemon_name, created_at, user_id) 
                VALUES 
                (
                    '" . $name . "', 
                    '" . $created_at . "', 
                    '" . $_SESSION['id'] . "'
                )"
            );
        $id =  self::$db->lastInsertRowid();

        header('Content-Type: application/json');
        return json_encode([
            'result'        => 'success',
            'id'            => $id,
            'created_at'    => $created_at,
            'user_id'       => $_SESSION['id'],
            'name'          => $_SESSION['name'],
        ]);
    }
}

//Turn beast on!
Controller::init($db);
Helper::init($db);

//clear flash errors
if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != HTTP_URL) {
    unset($_SESSION['errors']);
}

//find a route
$routeId = false;
foreach ($routes as $id => $route) {
    $pattern = [
        '/(\/{\?.*})/',
        '/({.+})/',
        '/\//'
    ];
    $replacement = [
        '(/.+)?',
        '(.+)',
        '\/'
    ];
    $route_regex = '/' . preg_replace($pattern, $replacement, $route['url']) . '/';
    
    preg_match($route_regex, $request, $res);
    if ($res && $res[0] == $request) {//if url match
        //get args keys
        $args = null;
        preg_match_all('/{\??(\w+)}/', $route['url'], $args);
        if (isset($args[1])) {
            $args = $args[1];
        }

        //get args values
        $args_catcher = '/' . preg_replace(
            [
                '/({\?.+})/', 
                '/({.+})/', 
                '/\//'
            ], 
            [
                '(\w+)',
                '(\w+)',
                '\/'
            ], 
            $route['url']
        ) . '/';
        preg_match_all($args_catcher, $request, $args_data);
        if (isset($args_data[1])) {
            $args_data = $args_data[1];
        }

        //get args array
        if ($args && $args_data) {
            $args = array_combine($args, $args_data);
        } else { 
            $args = [];
        }

        if (isset($route['method'])) {//if method also defined
            if ($_SERVER['REQUEST_METHOD'] == $route['method']) {//if method match
                $routeId = $id;
                break;    
            }
        } else {//if method undefined
            $routeId = $id;
            break;
        }
    }
}
if ($routeId === false) {
    echo Controller::not_found();
    return;
}
if (isset($routes[$routeId]['middleware']) && count($routes[$routeId]['middleware']) > 0) {
    foreach ($routes[$routeId]['middleware'] as $middleware) {
        $middleware_result = call_user_func(['Middleware', $middleware]);
        if ($middleware_result !== true) {
            echo Controller::not_found();
            return;
        }
    }
}
echo call_user_func(['Controller', $routes[$routeId]['controller']], $args);
?>