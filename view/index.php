<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include 'head.php';
    ?>
    <title>Pokebook</title>
</head>
<body>

<div class="wrapper" id='app'>
    <div class="index">
        <div class="actions">
            <div class="account">
                <img src="https://www.gravatar.com/avatar/<?= md5( strtolower( trim( $_SESSION['email'] ) ) ) ?>?d=identicon&size=300" alt="">
            </div>
            <h2><?= $_SESSION['name'] ?></h2>
            <a href='/logout' class="quit">quit</a>
            <div class="search">
                <input type="text" id="liveSearch" v-model="searchString" :class='[{success: pokemon}]' placeholder="Start your new pokemon learning here">
            </div>
            <div id="pokemon" v-if='pokemon'>
                <div class="pokemon_account_info">
                    <div class="pokemon_image">
                        <img :src="pokemon.sprites.front_default" alt="Pokemon image">
                    </div>
                    <h2 class="pokemon_name">
                        {{ pokemon.name }}
                    </h2>
                </div>
                <div class="pokemon_moves">
                    <div v-for="move in pokemon.moves">
                        {{ move.move.name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="table" id="history">
            <h1>The history</h1>
            <div class="history_table">
                <div class="header">
                    Pokemon name
                </div>
                <div class="header">
                    Searcher name
                </div>
                <div class="header">
                    Searched at
                </div>
                <div class="header">
                    Actions                        
                </div>
                <template v-for="(search, index) in searches">
                    <div class="pokemon-name" @click="searchString=search.pokemon_name">
                        <span>{{ search.pokemon_name }}</span>
                    </div>
                    <div class="searcher">
                        {{ search.name }}
                    </div>
                    <div class="date">
                        {{ search.created_at }}
                    </div>
                    <div class="actions">
                        <button v-if='search.user_id == <?= $_SESSION["id"] ?>' v-on:click="deleteAction(index)">Delete</button>
                    </div>
                </template>
            </div>
            <div class="table-pagination">
                <div class="prev" v-on:click='prev'>
                    Prev
                </div>
                <div class="page">
                    Page: {{ page }}-{{ pageMax }}
                </div>
                <div class="next" v-on:click='next'>
                    Next
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    include 'scripts.php';
    ?>
</body>
</html>