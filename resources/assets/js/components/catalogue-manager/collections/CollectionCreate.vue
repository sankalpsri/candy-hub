<script>
    export default {
        data() {
            return {
                request : apiRequest,
                createCollection: false,
                collection: this.baseCollection(),
                families: []
            }
        },
        mounted() {
        },
        computed: {
            name: {
                get() {
                    return this.collection.name[locale.current()];
                },
                set(value) {
                    this.collection.name[locale.current()] = value;
                }
            },
            sluggedName() {
                return this.collection.name[locale.current()].slugify();
            },
            url: {
                get() {
                    return this.collection.url.slugify();
                }
            }
        },
        methods: {
            save() {
                this.collection.url = this.collection.url.slugify();
                this.request.send('post', '/collections', this.collection)
                .then(response => {
                    CandyEvent.$emit('notification', {
                        level: 'success'
                    });
                    this.createCollection = false;
                    this.collection = this.baseCollection();
                    CandyEvent.$emit('collection-added', response.data);
                }).catch(response => {
                    CandyEvent.$emit('notification', {
                        level: 'error',
                        message: 'Missing / Invalid fields'
                    });
                });
            },
            baseCollection() {
                return {
                    name: {
                        [locale.current()] : ''
                    },
                    url : ''
                }
            }
        }
    }
</script>

<template>
    <div>
        <button class="btn btn-success" @click="createCollection = true"><i class="fa fa-plus fa-first" aria-hidden="true"></i> Add Collection</button>
        <candy-modal title="Create Collection" v-show="createCollection" size="modal-md" @closed="createCollection = false">
            <div slot="body">
                <div class="form-group">
                    <label for="name">Collection name</label>
                    <input type="text" class="form-control" id="name" v-model="name" @input="request.clearError('name')">
                    <span class="text-danger" v-if="request.getError('name')" v-text="request.getError('name')"></span>
                </div>
                <div class="form-group">
                    <label for="redirectURL">Collection URL</label>
                    <input type="text" id="redirectURL" class="form-control" v-model="collection.url">
                    <span class="text-info" v-if="collection.url">Your url will be sanitized to: <code>{{ url }}</code></span>
                    <span class="text-danger" v-if="request.getError('url')" v-text="request.getError('url')"></span>
                </div>
            </div>
            <template slot="footer">
                <button type="button" class="btn btn-primary" @click="save">Create collection</button>
            </template>
        </candy-modal>
    </div>
</template>
