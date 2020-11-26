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
        </section>
    </app-layout>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import { Inertia } from '@inertiajs/inertia'
    import debounce from 'lodash/debounce'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'

    export default {
        props: ['operation', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                magazins: [],
                produits: [],
                checkedRows: [],
                isSaving: false,
                loadingProducts: false,
                isModalProductActive: false,
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
            }
        },
    }
</script>