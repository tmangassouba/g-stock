<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="gerant">
                <b-button class="is-info is-small" icon-left="pencil" tag="a" :href="'/operations/'+ _operation.reference +'/modifier'">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteOperation">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <div>
                <b-field
                    horizontal 
                    label="Type">
                    <b-tag :type="tagType(_operation.type) + ' is-light'" rounded>{{ _operation.type }}</b-tag>
                </b-field>
                
                <b-field
                    horizontal 
                    label="Num. Référence">
                    {{ _operation.reference }}
                </b-field>
                
                <b-field
                    v-if="sortie || transfert"
                    horizontal 
                    label="Source">
                    {{ _operation.magazinFrom ? _operation.magazinFrom.name : '-' }}
                </b-field>
                
                <b-field
                    v-if="entrees || transfert"
                    horizontal 
                    label="Destination">
                    {{ _operation.magazinTo ? _operation.magazinTo.name : '' }}
                </b-field>

                <b-field
                    horizontal 
                    label="Date">
                    {{ _operation.date_formated }}
                </b-field>

                <b-field
                    horizontal 
                    label="Description">
                    {{ _operation.description ? _operation.description : '-' }}
                </b-field>
            </div>

            <br>

            <b-table
                :data="_operation.products"
                :striped="true"
                :hoverable="false"
                :narrowed="false"
                :mobile-cards="true">

                <!-- <template slot-scope="props"> -->
                    <b-table-column field="produit_name" label="Produit" v-slot="props">
                        <inertia-link :href="'/articles/' + props.row.code">
                            {{ props.row.designation ? props.row.designation : '-' }}
                        </inertia-link>
                    </b-table-column>

                    <b-table-column field="quantite" label="Quantité" :width="150" numeric v-slot="props">
                        {{ props.row.pivot ? props.row.pivot.quantite : '-' }}
                        <small v-if="props.row.unite"> <br> {{ props.row.unite.name }}</small>
                    </b-table-column>

                    <b-table-column field="piece" label="Nb. Pièce" :width="150" numeric :visible="entrees" v-slot="props">
                        {{ props.row.pivot ? props.row.pivot.piece : '-' }}
                    </b-table-column>

                    <b-table-column field="" label="P.U" :width="150" numeric :visible="entrees" v-slot="props">
                        {{ props.row.pivot ? props.row.pivot.prix : '-' }}
                    </b-table-column>

                    <b-table-column field="" label="Prix/Pièce" :width="150" numeric :visible="entrees" v-slot="props">
                        <span v-if="props.row.pivot">
                            {{ (props.row.pivot.prix / props.row.pivot.piece) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </span>
                    </b-table-column>

                    <b-table-column field="" label="Sous total" :width="150" numeric :visible="entrees" v-slot="props">
                        <span v-if="props.row.pivot">
                            {{ (props.row.pivot.prix * props.row.pivot.quantite) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </span>
                    </b-table-column>
                <!-- </template> -->
            </b-table>

            <div class="columns" v-if="entrees">
                <div class="column is-8">
                    <!-- {{ totalFacture }} -->
                </div>

                <div class="column is-4"  style="padding-top: 30px;">
                    <div class="info-row has-text-weight-bold" style="text-align:right;padding-right: 20px;">
                        <div class="has-text-grey">Total</div>
                        <div class="">
                            {{  totalFacture | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="columns">
                <div class="column is-6-">
                    <div class="level">
                        <div class="level-left"><h6 class="title is-4">Documents</h6></div>
                        <div class="level-right" v-if="gerant">
                            <div>
                                <b-tooltip type="is-dark" label="Télécharger des fichiers">
                                    <b-button type="is-info" size="is-small" icon-left="upload" @click="isModalFileActive = true"></b-button>
                                </b-tooltip>
                                    
                                <b-tooltip type="is-dark" label="Supprimer les fichiers sélectionnés">
                                    <b-button type="is-danger" size="is-small" icon-left="delete" @click="deleteDocs()" v-if="checkedFilesRows.length!=0"></b-button>
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
                        :checked-rows.sync="checkedFilesRows"
                        hoverable>
                        <b-table-column field="name" label="Document" v-slot="props">
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

                    <b-modal
                        v-if="_operation"
                        v-model="isModalFileActive"
                        trap-focus
                        :destroy-on-hide="false"
                        :can-cancel="['escape', 'x']"
                        :width="640"
                    >
                        <file-form :operation="_operation"  @reloadDocs="getDocs()" @close-file-modal="isModalFileActive = false"></file-form>
                    </b-modal>
                </div>
            </div>

            <b-notification :closable="false" class="loading-notification">
                <b-loading :is-full-page="true" v-model="isDeleting" :can-cancel="false"></b-loading>
            </b-notification>
        </section>
    </app-layout>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import { Inertia } from '@inertiajs/inertia'
    import debounce from 'lodash/debounce'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { FileForm } from "../../components/Operations"

    export default {
        props: ['operation', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
            FileForm
        },
        data() {
            return {
                magazins: [],
                produits: [],
                checkedRows: [],
                isSaving: false,
                loadingProducts: false,
                isDeleting: false,
                isModalProductActive: false,
                docs: [],
                loadingDocs: false,
                isModalFileActive: false,
                checkedFilesRows: [],
            }
        },

        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
            deleteOperation() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer opération',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> cette opération ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer opération',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/operations/delete', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Opération(s) supprimée(s) avec succès.',
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
            },
            tagType(type) {
                if (type == this.$OPERATION_SORTIE) {
                    return 'is-danger'
                }
                if (type == this.$OPERATION_TRANSFERT) {
                    return 'is-info'
                }
                return 'is-success'
            },

            getDocs() {
                this.loadingDocs = true
                axios.get('/operations/' + this._operation.reference +'/docs')
                .then((data) => {
                    this.docs = data.data.data
                    // console.log(data.data.data)
                })
                .finally(() => {
                    this.loadingDocs = false
                })
            },
            deleteDocs() {
                if (this.checkedFilesRows.length) {
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
                                checkedRows: this.checkedFilesRows
                            }
                            this.$inertia.post('/operations/' + this._operation.reference +'/delete-files', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    this.getDocs()
                                    this.checkedFilesRows = []
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
                organisation: 'parametres/getParametre',
            }),
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            title() {
                return 'Opération'
            },
            titleStack () {
                return ['Opération ' + (this._operation ? this._operation.reference : '') ]
            },
            _operation() {
                return this.operation && this.operation.data ? this.operation.data : null
            },
            entrees() {
                return this._operation && this._operation.type == this.$OPERATION_ENTREE
            },
            sortie() {
                return this._operation && this._operation.type == this.$OPERATION_SORTIE
            },
            transfert() {
                return this._operation && this._operation.type == this.$OPERATION_TRANSFERT
            },
            totalFacture: function () {
                let total = 0
                if (this._operation && this._operation.products) {
                    this._operation.products.forEach(produit => {
                        if (produit.pivot) {
                            total += produit.pivot.quantite * produit.pivot.prix
                        }
                    });
                }
                
                return total
            },
        },

        created() {
            this.getEntreprise()
            if (this._operation) {
                this.checkedRows[0] = this._operation
                this.getDocs()
            }
        },
    }
</script>