<template>
    <div>
        <nav-bar />
        <aside-menu :menu="menu" />
        <slot></slot>
        <footer-bar />

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
                    'Examples',
                    [
                        {
                            to: '/tables',
                            label: 'Tables',
                            icon: 'table',
                            updateMark: true
                        },
                        {
                            to: '/forms',
                            label: 'Forms',
                            icon: 'square-edit-outline'
                        },
                        {
                            to: '/profile',
                            label: 'Profile',
                            icon: 'account-circle'
                        },
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
                        }
                    ],
                    'About',
                    [
                        {
                            to: 'https://admin-one.justboil.me',
                            label: 'Premium Demo',
                            icon: 'credit-card'
                        },
                        {
                            to: 'https://justboil.me/bulma-admin-template/one',
                            label: 'About',
                            icon: 'help-circle'
                        }
                    ]
                ]
            }
        },
    }
</script>
