<template>
    <div>
        <nav-bar />
        <aside-menu :menu="menu" />
        <slot></slot>
        <!-- <footer-bar /> -->

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>
    </div>
    
</template>

<script>
    import NavBar from '../Menu/NavBar'
    import AsideMenu from '../Menu/AsideMenu'
    import FooterBar from '../Menu/FooterBar'

    export default {
        components: {
            NavBar,
            AsideMenu,
            FooterBar,
        },

        data() {
            return {
                showingNavigationDropdown: false,
                //menu: []
            }
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put('/current-team', {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },

            logout() {
                axios.post('/logout').then(response => {
                    window.location = '/';
                })
            },
        },

        computed: {
            path() {
                return window.location.pathname
            },
            menu () {
                return [
                    'General',
                    [
                        {
                            to: '/',
                            icon: 'desktop-mac',
                            label: 'Tableau de bord'
                        }
                    ],
                    'Articles',
                    [
                        {
                            to: '/articles',
                            label: 'Articles',
                            icon: 'cart-outline'
                        },
                        {
                            to: '/groupes-articles',
                            label: 'Groupes d\'articles',
                            icon: 'cart-plus'
                            /*updateMark: true*/
                        }/*,
                        {
                            label: 'Submenus',
                            subLabel: 'Submenus Example',
                            icon: 'view-list',
                            menu: [
                            {
                                to: '#void',
                                label: 'Sub-item One'
                            },
                            {
                                to: '#void',
                                label: 'Sub-item Two'
                            }
                            ]
                        }*/
                    ],
                    'Stock',
                    [
                        {
                            to: 'magazin',
                            label: 'Magazin',
                            icon: 'store-outline'
                        },
                        {
                            to: 'operations',
                            label: 'Opérations',
                            icon: 'credit-card'
                        }
                    ],
                    'Facturation',
                    [
                        {
                            to: 'factures',
                            label: 'Factures',
                            icon: 'file-document-outline'
                        },
                        {
                            to: 'clients',
                            label: 'Clients',
                            icon: 'account-circle-outline'
                        }
                    ]
                ]
            }
        },
    }
</script>
