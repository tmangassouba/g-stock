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
                <b-dropdown aria-role="list">
                    <b-button type="is-dark" size="is-small" icon-left="plus-circle" rounded slot="trigger"></b-button>

                    <!-- <b-dropdown-item aria-role="listitem">
                        <inertia-link href="/articles" >Articles</inertia-link>
                    </b-dropdown-item> -->
                    <b-dropdown-item aria-role="listitem" v-if="hasRole('ADMIN') || hasRole('GERANT')">
                        <inertia-link href="/operations/ajouter">Opération</inertia-link>
                    </b-dropdown-item>
                    <b-dropdown-item aria-role="listitem">
                        <inertia-link href="/factures/ajouter">Facture</inertia-link>
                    </b-dropdown-item>
                    <b-dropdown-item aria-role="listitem" v-if="hasRole('ADMIN') || hasRole('GERANT')">
                        <inertia-link href="/clients/ajouter">Client</inertia-link>
                    </b-dropdown-item>
                </b-dropdown>
            </div>
            
            <div class="navbar-item has-control no-left-space-touch">
                <!-- <div class="control"> -->
                <b-field label="" class="recherche">
                    <p class="control">
                        <b-dropdown v-model="cible">
                            <button class="button is-small" slot="trigger">
                                <span>Cible</span>
                                <!-- <b-icon icon="cog"></b-icon> -->
                            </button>

                            <b-dropdown-item value="Articles">Articles</b-dropdown-item>
                            <b-dropdown-item value="Clients">Clients</b-dropdown-item>
                            <b-dropdown-item value="Operations" v-if="hasRole('ADMIN') || hasRole('GERANT')">Opérations</b-dropdown-item>
                            <b-dropdown-item value="Factures">Factures</b-dropdown-item>
                        </b-dropdown>
                    </p>
                    <b-autocomplete
                        rounded
                        expanded
                        size="is-small"
                        v-model="search"
                        :data="data"
                        placeholder="Recherche ..."
                        icon="magnify"
                        max-height="300px"
                        clearable
                        :loading="isFetching"
                        @typing="getAsyncData"
                        @select="selectRow">
                        <template slot-scope="props">
                                <div v-if="cible == 'Articles'">{{ props.option.designation }}</div>
                                <div v-if="cible == 'Clients'">
                                    <div>{{ props.option.name }}</div>
                                    <div class="has-text-grey-light">{{ props.option.company }}</div>
                                </div>
                                <div v-if="cible == 'Operations'">{{ props.option.reference }}</div>
                                <div v-if="cible == 'Factures'">{{ props.option.reference }}</div>
                        </template>

                        <template slot="empty">Aucun résultat.</template>
                    </b-autocomplete>
                </b-field>
                <!-- </div> -->
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
                cible: 'Articles',
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
                axios.get('/search?search=' + search + '&cible=' + this.cible)
                    .then(({ data }) => {
                        this.data = data
                        // console.log(data)
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
                if (this.cible == 'Articles') {
                    Inertia.visit('/articles/' + option.code)
                }
                if (this.cible == 'Clients') {
                    Inertia.visit('/clients/' + option.code)
                }
                if (this.cible == 'Operations') {
                    Inertia.visit('/operations/' + option.reference)
                }
                if (this.cible == 'Factures') {
                    Inertia.visit('/factures/' + option.reference)
                }
            }
        }
    }
</script>