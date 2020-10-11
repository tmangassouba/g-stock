<template>
    <card-component title="Ajouter article"  @close="$emit('close')">
        <form @submit.prevent="submit">
            <b-field
                horizontal 
                label="Nom"
                :type="$page.errors.designation ? 'is-danger' : ''"
                :message=" $page.errors.designation ? $page.errors.designation[0] : ''"
                class="field-label is-small">
                <b-input name="designation" v-model="form.designation" size="is-small" expanded ></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Référence fabricant" 
                class="field-label is-small"
                :type="$page.errors.ref_fabricant ? 'is-danger' : ''"
                :message=" $page.errors.ref_fabricant ? $page.errors.ref_fabricant[0] : ''">
                <b-input name="ref_fabricant" v-model="form.ref_fabricant" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Stock Minimum" 
                class="field-label is-small"
                :type="$page.errors.stock_min ? 'is-danger' : ''"
                :message=" $page.errors.stock_min ? $page.errors.stock_min[0] : ''">
                <b-input name="stock_min" v-model="form.stock_min" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Stock Maximum" 
                class="field-label is-small"
                :type="$page.errors.stock_max ? 'is-danger' : ''"
                :message=" $page.errors.stock_max ? $page.errors.stock_max[0] : ''">
                <b-input name="stock_max" v-model="form.stock_max" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Unité" 
                class="field-label is-small"
                :type="$page.errors.unite_id ? 'is-danger' : ''"
                :message=" $page.errors.unite_id ? $page.errors.unite_id[0] : ''">
                <b-input name="unite_id" v-model="form.unite_id" size="is-small" expanded></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Quantité par unité" 
                class="field-label is-small"
                :type="$page.errors.quantite ? 'is-danger' : ''"
                :message=" $page.errors.quantite ? $page.errors.quantite[0] : ''">
                <b-select name="quantite" v-model="form.quantite" size="is-small" expanded placeholder="Quantité par unité" required>
                    <option :value="unite.id" v-for="unite in unites" :key="unite.id">{{ unite.name }}</option>
                </b-select>
                <!-- <b-input name="quantite" v-model="form.quantite" size="is-small" expanded></b-input> -->
            </b-field>

            <b-field 
                horizontal 
                label="Description" 
                class="field-label is-small"
                :type="$page.errors.description ? 'is-danger' : ''"
                :message=" $page.errors.description ? $page.errors.description[0] : ''">
                <b-input type="textarea" name="description" v-model="form.description" size="is-small" expanded></b-input>
            </b-field>

            <div style="text-align:right">
                <b-button size="is-small" type="is-info" native-type="submit" :loading="savingData">Ajouter</b-button>
                <b-button size="is-small" @click="$emit('close')">Annuler</b-button>
            </div>
        </form>
    </card-component>
</template>

<script>
    import { CardComponent } from "../Card";

    export default {
        name: 'ArticleForm',
        components: { CardComponent },
        data() {
            return {
                form: {
                    designation: null,
                    code: null,
                    ref_fabricant: null,
                    description: null,
                    stock_min: null,
                    stock_max: null,
                    unite_id: null,
                    prix: null,
                    quantite: null
                },
                savingData: false,
                unites: []
            }
        },
        methods: {
            submit() {
                this.savingData = true
                this.$inertia.post('/articles', this.form)
                .then(() => {
                    this.resetForm()
                    this.$buefy.notification.open({
                        message: 'Produit ajouté avec succès.',
                        type: 'is-success'
                    })
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.savingData = false
                })
            },
            resetForm() {
                this.form = {
                    designation: null,
                    code: null,
                    ref_fabricant: null,
                    description: null,
                    stock_min: null,
                    stock_max: null,
                    unite_id: null,
                    prix: null,
                    quantite: null
                }
            }
        },
        created() {
            axios.get('api/unites')
            .then(({data}) => {
                this.unites = data
            });
        },
    }
</script>
