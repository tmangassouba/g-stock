<template>
    <nav v-show="isNavBarVisible" id="navbar-main" class="navbar is-fixed-top">
        <div class="navbar-brand">
            <a
                class="navbar-item is-hidden-desktop"
                @click.prevent="menuToggleMobile"
            >
                <b-icon :icon="menuToggleMobileIcon" />
            </a>
            <div class="navbar-item has-control no-left-space-touch">
                <div class="control">
                    <b-field label="" class="recherche">
                        <b-autocomplete
                            rounded
                            expanded
                            size="is-small"
                            v-model="search"
                            :data="data"
                            placeholder="Recherche ..."
                            icon="magnify"
                            clearable
                            :loading="isFetching"
                            @typing="getAsyncData"
                            @select="selectRow">
                            <template slot-scope="props">
                                <!-- <inertia-link :href="'/articles/' + props.option.code"> -->
                                    {{ props.option.designation }}
                                <!-- </inertia-link> -->
                            </template>

                            <template slot="empty">Aucun résultat.</template>
                        </b-autocomplete>
                    </b-field>
                    <!-- <input class="input" placeholder="Search everywhere..." /> -->
                </div>
            </div>
        </div>
        <div class="navbar-brand is-right">
            <a
                class="navbar-item navbar-item-menu-toggle is-hidden-desktop"
                @click.prevent="menuNavBarToggle"
            >
                <b-icon :icon="menuNavBarToggleIcon" custom-size="default" />
            </a>
        </div>

        <div class="navbar-menu fadeIn animated faster" :class="{ 'is-active': isMenuNavBarActive }" >
            <div class="navbar-end">
                <nav-bar-menu class="has-divider" v-if="hasRole('ADMIN')">
                    <b-icon icon="cog" custom-size="default" />

                    <div slot="dropdown" class="navbar-dropdown">
                        <inertia-link href="/parametres/profil-organisation" class="navbar-item">
                            <b-icon icon="domain" custom-size="default"></b-icon>
                            <span>Profil Organisation</span>
                        </inertia-link>

                        <inertia-link href="/magazins" class="navbar-item is-active-">
                            <b-icon icon="store-outline" custom-size="default" />
                            <span>Magazins</span>
                        </inertia-link>

                        <inertia-link href="/users" class="navbar-item">
                            <b-icon icon="account-multiple-outline" custom-size="default"></b-icon>
                            <span>Utilisateurs et Rôles</span>
                        </inertia-link>

                        <hr class="navbar-divider" />

                        <inertia-link href="/parametres/unites" class="navbar-item">
                            <b-icon icon="chart-line-variant" custom-size="default"></b-icon>
                            <span>Unités</span>
                        </inertia-link>

                        <!-- <hr class="navbar-divider" /> -->

                        <inertia-link href="/devises" class="navbar-item">
                            <b-icon icon="cash-usd-outline" custom-size="default"></b-icon>
                            <span>Devises</span>
                        </inertia-link>

                        <!-- <inertia-link href="/parametres/modeles" class="navbar-item">
                            <b-icon icon="palette-outline" custom-size="default"></b-icon>
                            <span>Modèles</span>
                        </inertia-link> -->
                    </div>
                </nav-bar-menu>

                <nav-bar-menu class="has-divider has-user-avatar">
                    <user-avatar :avatar="authUser.avatar" v-if="authUser" />
                    <div class="is-user-name">
                        <span>{{ authUser ? authUser.first_name : 'User' }}</span>
                    </div>

                    <div slot="dropdown" class="navbar-dropdown">
                        <a href="/user/profile" class="navbar-item" exact-active-class="is-active" >
                            <b-icon icon="account" custom-size="default" />
                            <span>Profil</span>
                        </a>
                        <hr class="navbar-divider" />
                        <a class="navbar-item" title="Déconnexion" @click="logout" >
                            <b-icon icon="logout" custom-size="default"></b-icon>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </nav-bar-menu>

                <a class="navbar-item is-desktop-icon-only" title="Déconnexion" @click="logout" >
                    <b-icon icon="logout" custom-size="default" />
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </nav>
</template>

<script>
    import { mapGetters } from 'vuex'
    import NavBarMenu from './NavBarMenu'
    import UserAvatar from './UserAvatar'
    import debounce from 'lodash/debounce'
    import { Inertia } from '@inertiajs/inertia'

    export default {
        name: 'NavBar',
        components: {
            UserAvatar,
            NavBarMenu
        },
        data () {
            return {
                isMenuNavBarActive: false,
                // isNavBarVisible: true,
                // isAsideMobileExpanded: true,
                // userName: "Tidiane"
                search: null,
                isFetching: false,
                data: [],
                selected: null
            }
        },

        computed: {
            menuNavBarToggleIcon () {
                return this.isMenuNavBarActive ? 'close' : 'dots-vertical'
            },
            menuToggleMobileIcon () {
                return this.isAsideMobileExpanded ? 'backburger' : 'forwardburger'
            },
            ...mapGetters({
                isNavBarVisible: 'menu/isNavBarVisible', 
                isAsideMobileExpanded: 'menu/isAsideMobileExpanded', 
                authUser: 'user/authUser',
                hasRole: 'user/hasRole'
            })
        },
        mounted () {
            // this.$route.afterEach(() => {
                this.isMenuNavBarActive = false
            // })
        },

        methods: {
            menuToggleMobile () {
                this.$store.commit('menu/asideMobileStateToggle')
            },
            menuNavBarToggle () {
                this.isMenuNavBarActive = !this.isMenuNavBarActive
            },

            logout() {
                axios.post('/logout').then(response => {
                    window.location = '/';
                })
            },
            getAsyncData: debounce(function (search) {
                if (!search.length) {
                    this.data = []
                    return
                }
                this.isFetching = true
                axios.get('/articles/search?search=' + search)
                    .then(({ data }) => {
                        this.data = data.data
                        // console.log(data)
                        // data.results.forEach((item) => this.data.push(item))
                    })
                    .catch((error) => {
                        this.data = []
                        throw error
                    })
                    .finally(() => {
                        this.isFetching = false
                    })
            }, 500),
            selectRow(option) {
                Inertia.visit('/articles/' + option.code)
            }
        }
    }
</script>