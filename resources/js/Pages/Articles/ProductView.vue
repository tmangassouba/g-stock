<template>
    <app-layout>
        <title-bar :title-stack="titleStack" v-if="_product && _product.id">
            <div class="buttons is-right">
                <b-button class="is-info is-small" icon-left="pencil" @click="isModalActive = true">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteProducts">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <div v-if="_product && _product.id">
                <h6 class="title is-6">Vue d'ensemble</h6>

                <div class="columns">
                    <div class="column is-8 vue-ensemble">
                        <div class="columns">
                            <div class="column is-3 le-label">Référence</div>
                            <div class="column value">{{ _product.code }}</div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Référence fabriant</div>
                            <div class="column value">{{ _product.ref_fabricant ? _product.ref_fabricant : '-' }}</div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Unité</div>
                            <div class="column value">{{ _product.unite ? _product.unite.name : '-' }}</div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Quantité</div>
                            <div class="column value">
                                {{ _product.quantite ? _product.quantite + ' pcs par ' + (_product.unite ? _product.unite.name : '-') : '-' }}
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Stock minimal</div>
                            <div class="column value">{{ _product.stock_min ? _product.stock_min : '-' }}</div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Stock maximal</div>
                            <div class="column value">{{ _product.stock_max ? _product.stock_max : '-' }}</div>
                        </div>
                        <div class="columns">
                            <div class="column is-3 le-label">Prix de vente</div>
                            <div class="column value">{{ _product.prix ? _product.prix : '-' }} CFA</div>
                        </div>

                        <br>
                        <h6 class="title is-6">Emplacements des stocks</h6>
                        //

                        <h6 class="title is-6">Opérations</h6>
                        //

                        <h6 class="title is-6">Documents</h6>
                        //
                    </div>

                    <div class="column">
                        <div class="columns">
                            <div class="column">
                                <b-image src="https://picsum.photos/600/400" alt="A random image" ratio="6by4"></b-image>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <strong>Description : </strong> {{ _product.description ? _product.description : '-' }}
                            </div>
                        </div>

                        <section class="section is-main-section" style="background-color: #f3f3f3;">
                            <strong>Stock</strong> <br>
                            {{ _product }}
                        </section>
                    </div>
                </div>
            </div>

            <div v-else>Page introuvable !</div>
        </section>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm } from "../../components/Articles"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: [ 'product' ],
        
        components: {
            AppLayout,
            TitleBar,
            ArticleForm
        },

        data() {
            return {
                isModalActive: false,
                _product: {},
            }
        },

        methods: {
            deleteProducts() {
                //
            }
        },

        computed: {
            titleStack () {
                let title = (this._product && this._product.designation) ? this._product.designation : '-'
                return [ title ]
            }
        },

        created() {
            if (this.product && this.product.data) {
                this._product = this.product.data
            }
        },
    }
</script>
