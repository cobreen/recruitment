<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class passportAuthController extends Controller
{
    /**
     * handle user registration request
     */
    public function registerUser(Request $request) {
        $this->validate($request, [
            'name'          =>'required',
            'email'         =>'required|email|unique:users',
            'password'      =>'required|min:8',
            //We accept immortals and embryos, but not time-travelers
            //Since everybody usually lies on the internet about the age, we at least make sure the lie is plausible
            'birth_year'    =>'required|numeric|max:' . date("Y"),
            'file'          => 'required|mimes:jpeg,jpg,png|max:2048'
        ]);

        // return $request->birth_year;

        $fileName = time().'_'.$request->file->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

        $user= User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'image_path'    => $filePath,
            'birth_year'    => $request->birth_year
        ]);

        $access_token = $user->createToken(uniqid())->accessToken;
        //return the access token we generated in the above step
        return response()->json(['token'=>$access_token], 200);
    }

    /**
     * login user to our application
     */
    public function loginUser(Request $request) {
        $login_credentials = [
            'email'     => $request->email,
            'password'  => $request->password,
        ];
        if(auth()->attempt($login_credentials)) {
            //generate the token for the user
            $user_login_token= auth()->user()->createToken('PassportExample@Section.io')->accessToken;
            //now return this token on success login attempt
            return response()->json(['token' => $user_login_token], 200);
        } else {
            //wrong login credentials, return, user not authorised to our system, return error code 401
            return response()->json(['error' => 'UnAuthorised Access'], 401);
        }
    }

    public function updateUser(Request $request) {
        $this->validate($request, [
            'name'          =>'required',
            'email'         =>'required|email',
            //We accept immortals and embryos, but not time-travelers
            //Since everybody usually lies on the internet about the age, we at least make sure the lie is plausible
            'birth_year'    =>'required|numeric|max:' . date("Y"),
            'file'          => 'required|mimes:jpeg,jpg,png|max:2048'
        ]);

        $fileName = time().'_'.$request->file->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

        auth()
            ->user()
            ->fill([
                'name'          => $request->name,
                'email'         => $request->email,
                'image_path'    => $filePath,
                'birth_year'    => $request->birth_year
            ])
            ->save();
        //return the access token we generated in the above step
        return response()->json(['status' => "ok"], 200);
    }

    /**
     * This method returns authenticated user details
     */
    public function authenticatedUserDetails() {
        //returns details
        $res = auth()->user()->toArray();
        $res['image_path'] = asset('storage/' . $res['image_path']);
        return response()->json(['authenticated-user' => $res], 200);
    }
}
