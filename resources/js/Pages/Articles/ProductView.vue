<template>
    <app-layout>
        <title-bar :title-stack="titleStack" v-if="_product && _product.id">
            <div class="buttons is-right" v-if="hasRole('ADMIN')">
                <b-button class="is-info is-small" icon-left="pencil" @click="editProduct()">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteProduct">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <div v-if="_product && _product.id">
                <div class="columns">
                    <div class="column is-8 vue-ensemble">
                        <h6 class="title is-6">Vue d'ensemble</h6>

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
                                <div @click="isImageModalActive = true" style="cursor:pointer;border: 1px solid #d6d6d6;">
                                    <b-image
                                        :src="_product.image_url"
                                        :alt="_product.designation"
                                        ratio="6by4"
                                        >
                                    </b-image>
                                </div>
                                <b-modal v-model="isImageModalActive" :width="600">
                                    <p class="image"><img :src="_product.image_url"></p>
                                </b-modal>
                            </div>
                            <div class="column is-3" v-if="hasRole('ADMIN')">
                                <!-- <form @submit.prevent="editMode ? editData() : addData()" v-if="form"> -->
                                    <b-field>
                                        <b-upload v-model="file" drag-drop expanded :loading="changingImage" @input="changeImage">
                                            <!-- <section class="section"> -->
                                                <div class="content has-text-centered">
                                                    <p>
                                                        <b-icon icon="camera-outline"></b-icon>
                                                    </p>
                                                    <!-- <p>Drop your files here or click to upload</p> -->
                                                </div>
                                            <!-- </section> -->
                                        </b-upload>
                                    </b-field>
                                    <b-button
                                        type="is-danger is-light"
                                        icon-right="delete"
                                        size="is-small"
                                        expanded
                                        outlined
                                        v-if="_product.image">
                                    </b-button>
                                <!-- </form> -->
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <strong>Description : </strong> {{ _product.description ? _product.description : '-' }} <br>
                                <strong>Crée le : </strong> {{ _product.created_at ? _product.created_at : '-' }} <br>
                                <strong>Dernière modification : </strong> {{ _product.updated_at ? _product.updated_at : '-' }} <br>
                            </div>
                        </div>

                        <section class="section is-main-section" style="background-color: #f3f3f3;">
                            <strong>Stock</strong> <br>
                            {{ _product }}
                        </section>
                    </div>
                </div>

                <b-modal 
                    v-model="isModalActive"
                    trap-focus
                    :destroy-on-hide="false"
                    :can-cancel="['escape', 'x']"
                    :width="640"
                >
                    <article-form :article="_product" @close="isModalActive = false"></article-form>
                </b-modal>
            </div>

            <div v-else>Page introuvable !</div>

            <b-notification :closable="false" class="loading-notification">
                <b-loading :is-full-page="true" v-model="isDeleting" :can-cancel="false"></b-loading>
            </b-notification>
        </section>
    </app-layout>
</template>

<script>
    import { mapGetters } from 'vuex'
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
                isImageModalActive: false,
                isDeleting: false,
                changingImage: false,
                _product: {},
                file: null
            }
        },

        methods: {
            editProduct() {
                this.isModalActive = true
            },
            deleteProduct() {
                this.$buefy.dialog.confirm({
                    title: 'Supprimer article',
                    message: 'Etes-vous sûrs de vouloir <b>supprimer</b> cet article ?<br/> Cette action ne peut pas être annulée.',
                    confirmText: 'Supprimer article',
                    type: 'is-danger',
                    hasIcon: true,
                    size: 'is-small',
                    onConfirm: () => {
                        // this.$buefy.toast.open('Account deleted!')
                        this.isDeleting = true
                        this.$inertia.delete('/articles/delete/' + this._product.code)
                        .then(() => {
                            if (this.$page.flash.message != null ) {
                                this.resetForm()
                                this.$buefy.notification.open({
                                    message: 'Article supprimé avec succès.',
                                    type: 'is-success'
                                })
                            }
                        })
                        .catch(({message}) => {
                            // this.$handleMessage(message, 'danger');
                        }).finally(() => {
                            this.isDeleting = false
                        })
                    }
                })
            },
            changeImage(file) {
                // console.log(file)
                let data = new FormData()
                data.append('photo', file || '')
                this.changingImage = true
                Inertia.post('/articles/' + this._product.code + '/change-image', data)
                .then(() => {
                    this.$inertia.visit('/articles/' + this._product.code)
                })
            }
        },

        computed: {
            ...mapGetters({
                isAsideVisible: 'menu/isAsideVisible',
                hasRole: 'user/hasRole'
            }),
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
