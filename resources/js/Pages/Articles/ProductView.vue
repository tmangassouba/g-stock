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
                            <div class="column value">{{ _product.prix ? _product.prix : '-' }} {{ monaie }}</div>
                        </div>

                        <hr>

                        <strong>
                            <inertia-link :href="'/articles/' + _product.code + '/operations'">
                                Liste des opérations <b-icon icon="arrow-right" size="is-small"></b-icon>
                            </inertia-link>
                        </strong>

                        <hr>

                        <h6 class="title is-6">Emplacements des stocks</h6>
                        <b-table 
                            :data="magazins"
                            :loading="loadingMagazins"
                            striped
                            hoverable>
                            <b-table-column field="name" label="Magazins" v-slot="props">{{ props.row.name }}</b-table-column>
                            <b-table-column label="Stock" width='150' numeric v-slot="props">{{ props.row.stock }}</b-table-column>


                            <template slot="empty">
                                <section class="section-">
                                    <div class="content has-text-grey has-text-centered">
                                        <div><b-icon icon="inbox" size="is-medium"></b-icon></div>
                                        <p>Aucun résultat.</p>
                                    </div>
                                </section>
                            </template>
                        </b-table>

                        <hr>
                        
                        <div class="level">
                            <div class="level-left"><h6 class="title is-6">Documents</h6></div>
                            <div class="level-right" v-if="hasRole('ADMIN')">
                                <div>
                                    <b-tooltip type="is-dark" label="Télécharger des fichiers">
                                        <b-button type="is-info" size="is-small" icon-left="upload" @click="isModalFileActive = true"></b-button>
                                    </b-tooltip>
                                        
                                    <b-tooltip type="is-dark" label="Supprimer les fichiers sélectionnés">
                                        <b-button type="is-danger" size="is-small" icon-left="delete" @click="deleteDocs()" v-if="checkedRows.length!=0"></b-button>
                                    </b-tooltip>
                                </div>
                            </div>
                        </div>
                        <b-table 
                            :data="docs"
                            :loading="loadingDocs"
                            striped
                            :show-header="true"
                            checkable
                            :checked-rows.sync="checkedRows"
                            hoverable>
                            <b-table-column field="name" label="Document" v-slot="props">
                                <!-- <span>{{ props.row.extension }}</span> -->
                                <b-icon icon="file-document-outline" size="is-small"></b-icon>
                                <a :href="props.row.file_url" target="_blank" rel="">{{ props.row.name }}</a>
                            </b-table-column>
                            <b-table-column field="created_at" label="Date" numeric v-slot="props">
                                {{ props.row.created_at }}
                            </b-table-column>


                            <template slot="empty">
                                <section class="section_">
                                    <div class="content has-text-grey has-text-centered">
                                        <div><b-icon icon="inbox" size="is-medium"></b-icon></div>
                                        <p>Aucun document.</p>
                                    </div>
                                </section>
                            </template>
                        </b-table>

                        <!-- <h6 class="title is-6">Opérations</h6>
                        // -->
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
                                <b-field
                                    :message="$page.errors.photo ? $page.errors.photo[0] : ''"
                                    :type="{ 'is-danger' : $page.errors.photo }">
                                    <b-upload v-model="file" drag-drop expanded :loading="changingImage" @input="changeImage">
                                        <div class="content has-text-centered">
                                            <p>
                                                <b-icon icon="camera-outline"></b-icon>
                                            </p>
                                        </div>
                                    </b-upload>
                                </b-field>
                                <b-button
                                    v-if="_product.image"
                                    type="is-danger is-light"
                                    icon-right="delete"
                                    size="is-small"
                                    expanded
                                    outlined
                                    :loading="deletingImage"
                                    @click="deleteImage">
                                </b-button>
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
                            <!-- <br>
                            <div>
                                <strong>
                                    Stock d'ouverture
                                    <b-tooltip type="is-dark" label="Stock disponible au début de l'exercice.">
                                        <b-icon size="is-small" icon="help-circle-outline"></b-icon>
                                    </b-tooltip> :
                                </strong>
                                <span>{{ _product.stock_ouverture }}</span>
                            </div> -->
                            <br>
                            <div>
                                <strong>Stock disponible :</strong>
                                <!-- <br> -->
                                <span :class="_product.stock < _product.stock_min ? 'has-text-danger' : '' ">
                                    {{ _product.stock }}
                                </span>
                            </div>
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

                <b-modal 
                    v-model="isModalFileActive"
                    trap-focus
                    :destroy-on-hide="false"
                    :can-cancel="['escape', 'x']"
                    :width="640"
                >
                    <file-form :article="_product"  @reloadDocs="getDocs()" @close-file-modal="isModalFileActive = false"></file-form>
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
    import { mapGetters, mapActions } from 'vuex'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm, FileForm } from "../../components/Articles"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: [ 'product' ],
        
        components: {
            AppLayout,
            TitleBar,
            ArticleForm,
            FileForm
        },

        data() {
            return {
                isModalActive: false,
                isImageModalActive: false,
                isModalFileActive: false,
                isDeleting: false,
                changingImage: false,
                deletingImage: false,
                _product: {},
                file: null,
                magazins: [],
                loadingMagazins: false,
                docs: [],
                loadingDocs: false,
                checkedRows: []
            }
        },

        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
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
                                // this.resetForm()
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
                    if (this.$page.flash.message != null ) {
                        this.$inertia.visit('/articles/' + this._product.code)
                    }
                }).finally(() => {
                    this.changingImage = false
                })
            },
            deleteImage() {
                this.deletingImage = true
                this.$inertia.delete('/articles/' + this._product.code + '/delete-image')
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$inertia.visit('/articles/' + this._product.code)
                    }
                }).finally(() => {
                    this.deletingImage = false
                })
            },
            getMagazins() {
                this.loadingMagazins = true
                axios.get('/articles/' + this._product.code +'/stocks')
                .then((data) => {
                    this.magazins = data.data
                })
                .finally(() => {
                    this.loadingMagazins = false
                })
            },
            getDocs() {
                this.loadingDocs = true
                axios.get('/articles/' + this._product.code +'/docs')
                .then((data) => {
                    this.docs = data.data.data
                    // console.log(data.data.data)
                })
                .finally(() => {
                    this.loadingDocs = false
                })
            },
            deleteDocs() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer fichiers',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(s) fichier(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer fichier(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/articles/' + this._product.code +'/delete-files', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    this.getDocs()
                                    this.checkedRows = []
                                    this.$buefy.notification.open({
                                        message: 'Fichier(s) supprimé(s) avec succès.',
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
                }
            }
        },

        computed: {
            ...mapGetters({
                hasRole: 'user/hasRole',
                organisation: 'parametres/getParametre',
            }),
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            titleStack () {
                let title = (this._product && this._product.designation) ? this._product.designation : '-'
                return [ title ]
            }
        },

        created() {
            this.getEntreprise()
            if (this.product && this.product.data) {
                this._product = this.product.data
                this.getMagazins()
                this.getDocs()
            }
        },
    }
</script>
