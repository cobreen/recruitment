document.addEventListener("DOMContentLoaded", function(event) {
var vm = new Vue({
    el: '#app',
    data: {
        searchString: "",
        pokemon: null,
        page: 1,
        searches: [],
        pageMax: 1,
    },
    mounted () {
        this.getHistory();
    },
    created: function () {
        this.debouncedSearch = _.debounce(this.search, 500)
    },
    methods: {
        search () {
            this.pokemon = null;
            if (this.searchString) {
                jQuery.ajax({
                    url: 'https://pokeapi.co/api/v2/pokemon/' + this.searchString,
                    success: (res) => {
                        this.pokemon = res;
                        jQuery.ajax({
                            url: 'make-action/' + this.searchString,
                            success: (res) => {
                                console.log(res);
                                if (res.result == 'success') {
                                    this.searches.unshift({
                                        id: res.id,
                                        pokemon_name: this.searchString,
                                        created_at: res.created_at,
                                        user_id: res.user_id,
                                        name: res.name
                                    })
                                    console.log('unshifted', this.searches);
                                }
                            }
                        });
                    }
                })
            }
        },
        getHistory () {
            jQuery.ajax({
                url: '/get-history/' + this.page,
                success: (res) => {
                    this.searches = res.data;
                    this.page = res.page;
                    this.pageMax = res.pageMax;
                }
            })
        },
        deleteAction (id) {
            jQuery.ajax({
                url: '/delete-history/' + this.searches[id].id,
                success: (res) => {
                    if (res.result == 'success') {
                        this.searches.splice(id, 1);
                    }
                }
            })
        },
        prev () {
            if (this.page > 1) {
                this.page -= 1;
                this.getHistory();
            }
        },
        next () {
            if (this.page < this.pageMax) {
                this.page = parseInt(this.page) + 1;
                this.getHistory();
            }
        }
    },
    watch: {
        searchString (new_val, old_val) {
            this.debouncedSearch();
        }
    },
});
});