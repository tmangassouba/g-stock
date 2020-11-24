<template>
    <app-layout>
        <title-bar :title-stack="titleStack"></title-bar>

        <section class="section is-main-section dashboard">
            <div class="activites">
                <div class="columns">
                    <div class="column is-8">
                        <h6 class="title is-6">Activité</h6>
                        <div class="columns">
                            <div class="column is-3">
                                <inertia-link href="/articles" class="dashboard-item">
                                    <!-- <div class="icon"><b-icon icon="cart-outline" size="is-medium"/></div> -->
                                    <div class="chiffre text-blue">{{ products }}</div>
                                    <div class="name"><b-icon icon="cart-outline" size="is-small"/> Articles</div>
                                </inertia-link>
                            </div>
                            <div class="column is-3">
                                <inertia-link href="/operations" class="dashboard-item">
                                    <div class="chiffre text-red">{{ operations }}</div>
                                    <div class="name"><b-icon icon="credit-card" size="is-small"/> Operations</div>
                                    <!-- <div class="icon"><b-icon icon="credit-card" size="is-medium"/></div> -->
                                </inertia-link>
                            </div>
                            <!-- <div class="column is-3">//</div>
                            <div class="column is-3">//</div> -->
                        </div>
                    </div>
                    <div class="column is-4">
                        <h6 class="title is-6">Stock</h6>
                        <div class="columns stock">
                            <div class="column">QUANTITÉ DISPONIBLE</div>
                            <div class="column" style="text-align: right;font-weight: 700;">{{ stock }}</div>
                        </div>
                        <!-- <br> -->
                        <div class="columns stock">
                            <div class="column">Coût total</div>
                            <div class="column" style="text-align: right;font-weight: 700;">{{ couts | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="section is-main-section">//</section> -->
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import TitleBar from '../Menu/TitleBar'
    import { mapActions, mapGetters } from 'vuex'

    export default {
        props: ['operations', 'products', 'stock', 'couts'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                //
            }
        },
        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
        },
        computed: {
            ...mapGetters({
                organisation: 'parametres/getParametre',
            }),
            monaie() {
                return this.organisation ? this.organisation.devise : ''
            },
            titleStack () {
                return ['Tableau de bord']
            }
        },
        created() {
            this.getEntreprise()
        },
    }
</script>
