<template>
    <card-component :title=" editMode ? 'Modifier article' : 'Ajouter article' "  @close="$emit('close')">
        <form @submit.prevent="editMode ? editData() : addData()" v-if="form">
            <b-field
                horizontal 
                label="Nom"
                :type="$page.errors.designation ? 'is-danger' : ''"
                :message="$page.errors.designation ? $page.errors.designation[0] : ''"
                class="field-label is-small">
                <b-input name="designation" v-model="form.designation" size="is-small" required expanded ></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Référence fabricant" 
                class="field-label is-small"
                :type="$page.errors.ref_fabricant ? 'is-danger' : ''"
                :message="$page.errors.ref_fabricant ? $page.errors.ref_fabricant[0] : ''">
                <b-input name="ref_fabricant" v-model="form.ref_fabricant" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Stock Minimum" 
                class="field-label is-small"
                :type="$page.errors.stock_min ? 'is-danger' : ''"
                :message="$page.errors.stock_min ? $page.errors.stock_min[0] : ''">
                <b-input name="stock_min" v-model="form.stock_min" size="is-small" required expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Stock Maximum" 
                class="field-label is-small"
                :type="$page.errors.stock_max ? 'is-danger' : ''"
                :message="$page.errors.stock_max ? $page.errors.stock_max[0] : ''">
                <b-input name="stock_max" v-model="form.stock_max" size="is-small" expanded></b-input>
            </b-field>

            <!-- <b-field 
                horizontal
                class="field-label is-small"
                :type="$page.errors.stock_ouverture ? 'is-danger' : ''"
                :message="$page.errors.stock_ouverture ? $page.errors.stock_ouverture[0] : ''">
                <template slot="label">
                    Stock d'ouverture
                    <b-tooltip type="is-dark" label="Stock disponible au début.">
                        <b-icon size="is-small" icon="help-circle-outline"></b-icon>
                    </b-tooltip>
                </template>
                <b-input name="stock_ouverture" v-model="form.stock_ouverture" size="is-small" expanded></b-input>
            </b-field> -->

            <b-field 
                horizontal 
                label="Unité" 
                class="field-label is-small"
                :type="$page.errors.unite_id ? 'is-danger' : ''"
                :message="$page.errors.unite_id ? $page.errors.unite_id[0] : ''">
                <b-select name="unite_id" v-model="form.unite_id" size="is-small" expanded placeholder="Unité" required>
                    <option :value="unite.id" v-for="unite in unites" :key="unite.id">{{ unite.name }}</option>
                </b-select>
                <!-- <b-input name="unite_id" v-model="form.unite_id" size="is-small" expanded></b-input> -->
            </b-field>

            <b-field 
                horizontal
                class="field-label is-small"
                :type="$page.errors.quantite ? 'is-danger' : ''"
                :message="$page.errors.quantite ? $page.errors.quantite[0] : ''">
                <template slot="label">
                    Quantité
                    <b-tooltip type="is-dark" label="Ex: 1pcs/Carton">
                        <b-icon size="is-small" icon="help-circle-outline"></b-icon>
                    </b-tooltip>
                </template>
                <b-input name="quantite" v-model="form.quantite" size="is-small" required expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Prix de vente" 
                class="field-label is-small"
                :type="$page.errors.prix ? 'is-danger' : ''"
                :message="$page.errors.prix ? $page.errors.prix[0] : ''">
                <b-input name="prix" v-model="form.prix" size="is-small" required expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Description" 
                class="field-label is-small"
                :type="$page.errors.description ? 'is-danger' : ''"
                :message="$page.errors.description ? $page.errors.description[0] : ''">
                <b-input type="textarea" name="description" v-model="form.description" size="is-small" expanded></b-input>
            </b-field>

            <b-field horizontal class="field-label is-small">
                <div style="text-align:right">
                    <b-button size="is-small" type="is-info" native-type="submit" :loading="savingData">{{ editMode ? 'Modifier' : 'Ajouter'}}</b-button>
                    <b-button size="is-small" @click="$emit('close')">Annuler</b-button>
                </div>
            </b-field>
        </form>
    </card-component>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia'
    import { CardComponent } from "../Card";

    export default {
        name: 'ArticleForm',
        props:{
            article: {
                type: Object,
                required: false,
                // default: {}
            }
        },
        components: { CardComponent },
        data() {
            return {
                savingData: false,
                unites: []
            }
        },
        computed: {
            editMode: function() {
                return this.article && this.article.id
            },
            form: function() {
                if (this.article && this.article.id) {
                    return {
                        id: this.article.id,
                        designation: this.article.designation,
                        code: this.article.code,
                        ref_fabricant: this.article.ref_fabricant,
                        description: this.article.description,
                        stock_min: this.article.stock_min,
                        stock_max: this.article.stock_max,
                        stock_ouverture: this.article.stock_ouverture,
                        unite_id: this.article.unite_id,
                        prix: this.article.prix,
                        quantite: this.article.quantite
                    }
                }
                return {
                    id: null,
                    designation: null,
                    code: null,
                    ref_fabricant: null,
                    description: null,
                    stock_min: null,
                    stock_max: null,
                    stock_ouverture: null,
                    unite_id: null,
                    prix: null,
                    quantite: null
                }
            }
        },
        methods: {
            addData() {
                this.savingData = true
                this.$inertia.post('/articles', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$emit('resetData')
                        this.$buefy.notification.open({
                            message: 'Produit ajouté avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.savingData = false
                })
            },
            editData() {
                this.savingData = true
                this.$inertia.put('/articles/' + this.form.code, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$emit('updatData')
                        Inertia.visit(window.location.pathname)
                        this.$buefy.notification.open({
                            message: 'Produit modifié avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.savingData = false
                })
            }
        },
        created() {
            axios.get('/api/unites')
            .then(({data}) => {
                this.unites = data
            });
        },
    }
</script>
