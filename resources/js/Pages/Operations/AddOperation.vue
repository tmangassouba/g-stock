<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="gerant">
                <span v-if="editMode">
                    <b-button class="is-info is-small" icon-left="arrow-left" tag="a" :href="'/operations/' + _operation.reference">Annuler</b-button>
                    <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteOperation">Supprimer</b-button>
                </span>
                <b-button class="is-info is-small" icon-left="arrow-left" tag="a" href="/operations" v-else>Annuler</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <form @submit.prevent="editMode ? updateData() : addData()" v-if="form">
                <!-- {{ form }} -->
                <div class="columns">
                    <div class="column is-6">
                        
                        <b-field
                            horizontal 
                            label="Type"
                            :type="$page.errors.type ? 'is-danger' : ''"
                            :message="$page.errors.type ? $page.errors.type[0] : ''"
                            class="field-label is-small">
                            <div>
                                <b-radio name="type" v-model="form.type" :native-value="$OPERATION_ENTREE" type="is-info" :disabled="editMode" required> {{ $OPERATION_ENTREE }} </b-radio> <br>
                                <b-radio name="type" v-model="form.type" :native-value="$OPERATION_SORTIE" type="is-info" :disabled="editMode" required> {{ $OPERATION_SORTIE }} </b-radio> <br>
                                <b-radio name="type" v-model="form.type" :native-value="$OPERATION_TRANSFERT" type="is-info" :disabled="editMode" required> {{ $OPERATION_TRANSFERT }} </b-radio>
                            </div>
                        </b-field>
                        
                        <b-field
                            v-if="sortie || transfert"
                            horizontal 
                            label="Source"
                            :type="$page.errors.magazin_from_id ? 'is-danger' : ''"
                            :message="$page.errors.magazin_from_id ? $page.errors.magazin_from_id[0] : ''"
                            class="field-label is-small">
                            <b-select placeholder="Source" name="magazin_from_id" v-model="form.magazin_from_id" size="is-small" required expanded>
                                <option
                                    v-for="magazin in magazins"
                                    :value="magazin.id"
                                    :key="magazin.id">
                                    {{ magazin.name }}
                                </option>
                            </b-select>
                        </b-field>
                        
                        <b-field
                            v-if="entrees || transfert"
                            horizontal 
                            label="Destination"
                            :type="$page.errors.magazin_to_id ? 'is-danger' : ''"
                            :message="$page.errors.magazin_to_id ? $page.errors.magazin_to_id[0] : ''"
                            class="field-label is-small">
                            <b-select placeholder="Destination" name="magazin_to_id" v-model="form.magazin_to_id" size="is-small" required expanded>
                                <option
                                    v-for="magazin in magazinsFor"
                                    :value="magazin.id"
                                    :key="magazin.id">
                                    {{ magazin.name }}
                                </option>
                            </b-select>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Date"
                            :type="$page.errors.date ? 'is-danger' : ''"
                            :message="$page.errors.date ? $page.errors.date[0] : ''"
                            class="field-label is-small">
                            <b-datepicker
                                v-model="form.date"
                                locale="fr-FR"
                                placeholder="Date"
                                icon="calendar-today"
                                size="is-small"
                                trap-focus>
                            </b-datepicker>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Description"
                            :type="$page.errors.description ? 'is-danger' : ''"
                            :message="$page.errors.description ? $page.errors.description[0] : ''"
                            class="field-label is-small">
                            <b-input name="description" v-model="form.description" expanded type="textarea"></b-input>
                        </b-field>

                    </div>

                    <div class="column"></div>
                </div>

                <b-table
                    :data="form.products"
                    :striped="true"
                    :hoverable="false"
                    :narrowed="false"
                    :mobile-cards="true">

                    <!-- <template slot-scope="props"> -->
                        <b-table-column field="produit_name" label="Produit" v-slot="props">
                            <!-- <b-input name="produit" v-model="props.row.produit" size="is-small"></b-input> -->
                            <b-autocomplete
                                :data="produits"
                                field="designation"
                                :loading="loadingProducts"
                                name="produit_name"
                                size="is-small"
                                v-model="props.row.produit_name"
                                @typing="getAsyncData"
                                required
                                @select="option => {props.row.produit_id = option ? option.id : null; props.row.piece = option ? option.quantite : 1}">
                                <template slot-scope="props">{{ props.option.designation }}</template>
                                <template slot="header">
                                    <a @click="isModalProductActive = true">
                                        <span> Nouvel produit </span>
                                    </a> 
                                </template>
                                <template slot="empty">Aucun résultat.</template>
                            </b-autocomplete>
                        </b-table-column>

                        <b-table-column field="quantite" label="Quantité" :width="150" numeric v-slot="props">
                            <b-input name="quantite" v-model="props.row.quantite" type="number" size="is-small" required></b-input>
                        </b-table-column>

                        <b-table-column field="piece" label="Nb. Pièce" :width="150" numeric :visible="entrees" v-slot="props">
                            <b-input name="piece" v-model="props.row.piece" type="number" size="is-small" required></b-input>
                        </b-table-column>

                        <b-table-column field="" label="P.U" :width="150" numeric :visible="entrees" v-slot="props">
                            <b-input name="prix" v-model="props.row.prix" type="number" size="is-small" required></b-input>
                        </b-table-column>

                        <b-table-column field="" label="Prix/Pièce" :width="150" numeric :visible="entrees" v-slot="props">
                            {{ (props.row.prix / props.row.piece) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </b-table-column>

                        <b-table-column field="" label="Sous total" :width="150" numeric :visible="entrees" v-slot="props">
                            {{ (props.row.prix * props.row.quantite) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </b-table-column>

                        <b-table-column field="" label="" :width="50" align="right" v-slot="props">
                            <b-button icon-left="delete" size="is-small is-light" type="is-danger" @click="removeData(props.index)" :disabled="form.products.length <= 1"></b-button>
                        </b-table-column>
                    <!-- </template> -->
                </b-table>

                <b-button type="is-text" @click="addLine">Ajouter une ligne</b-button>

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
                <b-field>
                    <b-button size="is-small" type="is-info" native-type="submit" :loading="isSaving">{{ title }}</b-button>
                </b-field>
            </form>

            <b-modal 
                v-model="isModalProductActive"
                trap-focus
                :destroy-on-hide="false"
                :can-cancel="['escape', 'x']"
                :width="640"
            >
                <article-form :article="{}" @resetData="selectArticle" @close="isModalProductActive = false"></article-form>
            </b-modal>
        </section>
    </app-layout>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import { Inertia } from '@inertiajs/inertia'
    import debounce from 'lodash/debounce'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm } from "../../components/Articles"

    export default {
        props: ['operation', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
            ArticleForm
        },
        data() {
            return {
                magazins: [],
                produits: [],
                checkedRows: [],
                isSaving: false,
                loadingProducts: false,
                isModalProductActive: false,
                form: {
                    id: null,
                    reference: null,
                    magazin_from_id: null,
                    magazin_to_id: null,
                    description: null,
                    type: null,
                    date: new Date(),
                    user_id: null,
                    products: [
                        {
                            produit_id: null,
                            produit_name: null,
                            quantite: 1,
                            prix: 1,
                            piece: 1,
                        }
                    ]
                }
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
            addData() {
                this.isSaving = true
                this.$inertia.post('/operations', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            updateData() {
                this.isSaving = true
                this.$inertia.put('/operations/' + this._operation.reference + '/update', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Modifiée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            addLine() {
                this.form.products.push(
                    {
                        produit_id: null,
                        produit_name: null,
                        quantite: 1,
                        prix: 1,
                        piece: 1,
                    }
                )
            },
            getAsyncData: debounce(function (name) {
                if (!name.length) {
                    return
                }
                this.loadingProducts = true
                axios.get('/api/produits', { params: { recherche: name } })
                .then(({data}) => {
                    this.produits = data.data
                })
                .catch(response => {
                    this.produits = []
                    this.$handleMessage(response.message, 'danger')
                })
                .finally(() => {
                    this.loadingProducts = false
                });
            }, 500),
            removeData(index) {
                if (this.form.products && this.form.products.length > 1) {
                    this.form.products.splice(index, 1)
                }
            },
            selectArticle() {
                //
            },
        },

        computed: {
            ...mapGetters({
                organisation: 'parametres/getParametre',
            }),
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            title() {
                return this.editMode ? 'Modifier' : 'Ajouter'
            },
            titleStack () {
                return [this.title + ' operation']
            },
            editMode() {
                return this._operation && this._operation.id ? true : false;
            },
            _operation() {
                return this.operation && this.operation.data ? this.operation.data : null
            },
            magazinsFor() {
                const mags = this.magazins.filter(item => item.id != this.form.magazin_from_id)
                return mags
            },
            entrees() {
                return this.form.type && this.form.type == this.$OPERATION_ENTREE
            },
            sortie() {
                return this.form.type && this.form.type == this.$OPERATION_SORTIE
            },
            transfert() {
                return this.form.type && this.form.type == this.$OPERATION_TRANSFERT
            },
            totalFacture: function () {
                let total = 0
                if (this.form && this.form.products) {
                    this.form.products.forEach(produit => {
                        total += produit.quantite * produit.prix
                    });
                }
                return total
            },
        },

        created() {
            this.getEntreprise()
            axios.get('/api/magazins')
            .then((data) => {
                this.magazins = data.data
            })
            // console.log(this.operation.data)

            if (this._operation) {
                this.form.id              = this._operation.id
                this.form.reference       = this._operation.reference
                this.form.magazin_from_id = this._operation.magazin_from_id
                this.form.magazin_to_id   = this._operation.magazin_to_id
                this.form.description     = this._operation.description
                this.form.type            = this._operation.type
                this.form.user_id         = this._operation.user_id
                let index = 0
                
                this._operation.products.forEach(element => {
                    let prod = {
                        produit_id: element.pivot ? element.id : null,
                        produit_name: element.pivot ? element.designation : null,
                        quantite: element.pivot ? element.pivot.quantite : null,
                        prix: element.pivot ? element.pivot.prix : null,
                        piece: element.pivot ? element.pivot.piece : null,
                    }
                    this.form.products[index] = prod
                    index ++
                });

                this.checkedRows[0] = this._operation
            }
        },
    }
</script>