<template>
    <aside v-show="isAsideVisible" class="aside is-placed-left is-expanded">
        <aside-tools :is-main-menu="true">
            <span slot="label"> <b>Tii</b>Stock </span>
        </aside-tools>
        <div class="menu is-menu-main">
            <!-- GENERAL -->
            <p class="menu-label">GENERAL</p>
            <ul class="menu-list">
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="desktop-mac" custom-size="default" />
                        <span class="menu-item-label">Tableau de bord</span>
                    </inertia-link>
                </li>
            </ul>

            <!-- Articles -->
            <p class="menu-label">Articles</p>
            <ul class="menu-list">
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/articles" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="cart-outline" custom-size="default" />
                        <span class="menu-item-label">Articles</span>
                    </inertia-link>
                </li>
            </ul>

            <!-- Stock -->
            <p class="menu-label">Stock</p>
            <ul class="menu-list">
                <!-- <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/magazin" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="store-outline" custom-size="default" />
                        <span class="menu-item-label">Magazin</span>
                    </inertia-link>
                </li> -->
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/operations" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="credit-card" custom-size="default" />
                        <span class="menu-item-label">Opérations</span>
                    </inertia-link>
                </li>
            </ul>

            <!-- Facturation -->
            <p class="menu-label">Facturation</p>
            <ul class="menu-list">
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/factures" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="file-document-outline" custom-size="default" />
                        <span class="menu-item-label">Factures</span>
                    </inertia-link>
                </li>
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/clients" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="account-circle-outline" custom-size="default" />
                        <span class="menu-item-label">Clients</span>
                    </inertia-link>
                </li>
            </ul>

            <!-- Administration -->
            <!-- <p class="menu-label" v-if="hasRole('ADMIN')">Administration</p>
            <ul class="menu-list" v-if="hasRole('ADMIN')">
                <li :class="{ 'is-active': isDropdownActive }">
                    <inertia-link href="/users" exact-active-class="is-active" class="has-icon">
                        <b-icon icon="account-multiple-outline" custom-size="default" />
                        <span class="menu-item-label">Utilisateurs</span>
                    </inertia-link>
                </li>
            </ul> -->
        </div>
    </aside>
</template>

<script>
    import { mapGetters } from 'vuex'
    import AsideTools from './AsideTools'

    export default {
        name: 'AsideMenu',
        components: { AsideTools },
        data() {
            return {
                // isAsideVisible: true
                isSubmenuList: true,
                isDropdownActive: false
            }
        },
        computed: {
            ...mapGetters({
                isAsideVisible: 'menu/isAsideVisible',
                // hasRole: 'user/hasRole'
            }),
        },
        methods: {
            menuClick (item) {
                //
            }
            // menuClick (item) {
            //     this.$emit('menu-click', item)
            // }
        }
    }
</script>
