<template>
    <card-component :title=" editMode ? 'Modifier utilisateur' : 'Ajouter utilisateur' "  @close="$emit('close')">
        <form @submit.prevent="editMode ? editData() : addData()" v-if="form">
            <b-field
                horizontal 
                label="Prénom"
                :type="$page.errors.first_name ? 'is-danger' : ''"
                :message="$page.errors.first_name ? $page.errors.first_name[0] : ''"
                class="field-label is-small">
                <b-input name="first_name" v-model="form.first_name" size="is-small" required expanded ></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Nom" 
                class="field-label is-small"
                :type="$page.errors.last_name ? 'is-danger' : ''"
                :message="$page.errors.last_name ? $page.errors.last_name[0] : ''">
                <b-input name="last_name" v-model="form.last_name" size="is-small" required expanded></b-input>
            </b-field>

            <b-field
                horizontal
                label="Email" 
                class="field-label is-small"
                :type="$page.errors.email ? 'is-danger' : ''"
                :message="$page.errors.email ? $page.errors.email[0] : ''">
                <b-input type="email" name="email" v-model="form.email" size="is-small" required expanded :disabled="editMode"></b-input>
            </b-field>

            <b-field 
                horizontal 
                label="Téléphone" 
                class="field-label is-small"
                :type="$page.errors.phone ? 'is-danger' : ''"
                :message="$page.errors.phone ? $page.errors.phone[0] : ''">
                <b-input name="phone" v-model="form.phone" size="is-small" expanded></b-input>
            </b-field>

            <b-field
                v-if="editMode"
                horizontal 
                label="Actif" 
                class="field-label is-small"
                :type="$page.errors.actif ? 'is-danger' : ''"
                :message="$page.errors.actif ? $page.errors.actif[0] : ''">
                <b-switch
                    name="actif"
                    v-model="form.actif"
                    :true-value="1"
                    :false-value="0"
                    type="is-success"
                    size="is-small-" ></b-switch>
            </b-field>

            <b-field 
                horizontal 
                label="Rôles" 
                class="field-label is-small"
                :type="$page.errors.roles ? 'is-danger' : ''"
                :message="$page.errors.roles ? $page.errors.roles[0] : ''">
                <b-checkbox-button
                    v-for="(role, index) in roles"
                    :key="index"
                    name="roles"
                    v-model="form.roles"
                    :native-value="role.id"
                    type="is-success"
                    size="is-small">
                    <span>{{ role.name }}</span>
                </b-checkbox-button>
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
    import { CardComponent } from "../Card";

    export default {
        name: 'UserForm',
        props:{
            user: {
                type: Object,
                required: false,
                // default: {}
            }
        },
        components: { CardComponent },
        data() {
            return {
                savingData: false,
                roles: [],
                submited: 0
            }
        },
        computed: {
            editMode: function() {
                return this.user && this.user.id
            },
            form: function() {
                if (this.user && this.user.id) {
                    return {
                        id: this.user.id,
                        first_name: this.user.first_name,
                        last_name: this.user.last_name,
                        email: this.user.email,
                        actif: this.user.actif,
                        phone: this.user.phone,
                        roles: this.user.roles_id,
                        submited: this.submited
                    }
                }
                return {
                    id: null,
                    first_name: null,
                    last_name: null,
                    email: null,
                    actif: null,
                    phone: null,
                    roles: [],
                    submited: this.submited
                }
            }
        },
        methods: {
            addData() {
                this.savingData = true
                this.$inertia.post('/users', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        // this.resetForm()
                        this.$emit('resetData')
                        this.$buefy.notification.open({
                            message: 'Utilisateur ajouté avec succès.',
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
                this.$inertia.put('/users/' + this.form.id, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Utilisateur modifié avec succès.',
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
            axios.get('api/roles')
            .then(({data}) => {
                this.roles = data
            });
        },
    }
</script>
