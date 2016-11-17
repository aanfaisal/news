<template>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<section class="content">
    <div class="row">
        <div class="col-md-12">
          <a v-if="post.status == 'approved'" href="/blog/{{post.slug}}" target="blank"  class="btn btn-lg btn-flat btn-success" role="button" style="margin-bottom: 15px;">
            View Post
          </a>
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Posting Edit
                        <small>Markdown Editor</small>
                    </h3>
                </div>
                <div class="box-body pad">
                    <div class="form-horizontal" enctype="multipart/form-data">
                      <div class="form-group">
                        <label for="title" class="col-sm-1 control-label">Judul Berita</label>
                        <div class="col-sm-11">
                          <input class="form-control" id="title" placeholder="title" v-model="post.title">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="categories" class="col-sm-1 control-label">Pilih kategori</label>
                        <div class="col-sm-11">
                            <multiselect :selected.sync="post.categories.data" :options="options2" :multiple="true"
                            key="hashid" label="name" :on-change="onChangeAction"
                            >

                            </multiselect>
                        </div>
                      </div>
                      <div class="form-group">
                          <label for="image" class="col-sm-1 control-label">Gambar</label>
                          <div class="col-sm-5">
                              <dropzone :model='post.image' :action="'/api/posts/'+postId+'/image'"></dropzone>
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="description" class="col-sm-1 control-label">Deskripsi</label>
                        <div class="col-sm-11">
                          <input class="form-control" id="description" placeholder="description"
                          v-model="post.description">
                        </div>
                      </div>
                        <textarea id="mdeditor" name="content"></textarea>
                        <button v-if="isPublished" type="button" @click="publishPost(post)" class="btn btn-lg btn-success btn-flat pull-right">
                            Publish Post
                        </button>
                        <button type="button" @click="updatePost(post)" class="btn btn-lg btn-primary btn-flat pull-right">Simpan Post</button>
                        <button class="btn btn-danger btn-flat btn-lg"  @click="deletePost(post)">Hapus
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col-->
    </div>
    <!-- ./row -->
</section>
</template>
<script>
import SimpleMDE from 'simplemde'
import Dropzone from './Dropzone.vue'
import Multiselect from 'vue-multiselect/lib/vue-multiselect.js'
import {stack_bottomright, show_stack_success, show_stack_error} from '../Pnotice.js'

export default {
    created() {
        this.postId = this.$route.params.hashid
        this.fetchPost()
        this.fetchCatetgories()
    },
    components: {
        Dropzone,
        Multiselect
    },
    ready(){
        this.simplemde = new SimpleMDE({
            element: document.getElementById("mdeditor"),
            spellChecker: false,
        });
    },
    data () {
        return {
            post: {
                categories: {
                    data: []
                }
            },
            postId: '',
            simplemde: '',
            options2: [],
            values: []
        }
    },
    methods: {
        fetchPost () {
            this.$http({
                url: '/api/posts/' + this.postId + '?include=categories',
                method: 'GET'
            }).then(function (response) {
                this.$set('post', response.data)
                this.simplemde.value(this.post.content);
                this.$set('values', response.data.categories.data)
            })
        },
        updatePost (post) {
            post.content = this.simplemde.value()
            return new Promise((resolve, reject) => {
                this.$http.patch('/api/posts/' + post.hashid, post).then((response) => {
                    show_stack_success('Post Tersimpan', response)
                    resolve()
                }, function (response) {
                    show_stack_error('Gagal menyimpan posting!', response)
                    reject()
                });
            })
        },
        publishPost (post) {
            if (this.values === undefined || this.values == 0) {
                swal('Sorry', 'Tambah kategori sebelum memposting!', 'info')
            } else {
                let self = this
                swal({
                    title: 'Anda akan Memposting ini!',
                    text: 'Anda yakin ?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, posting ini!',
                    cancelButtonText: 'Tidak.',
                }).then(function () {
                    self.updatePost(post).then(() => {
                        self.$http.post('/api/posts/' + post.hashid + '/publish', post).then(function (response) {
                            swal(
                                    'Telah Terpublish!',
                                    'Potingan telah terpublish ke dunia :)!.',
                                    'success'
                            );
                            this.$router.go('/posts/')
                        }, function (response) {
                            show_stack_error('Failed to publish post!', response)
                        })
                    })
                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay', 'close', 'timer'
                    if (dismiss === 'cancel') {
                        swal(
                                'Tidak Jadi',
                                'Postingan Anda aman :)',
                                'error'
                        );
                    }
                });
            }
        },
        fetchCatetgories () {
            this.$http({url: '/api/categories', method: 'GET'}).then(function (response) {
                this.$set('options2', response.data.data)
            })
        },
        onChangeAction (value) {
            this.values = value
            this.$http.patch('/api/posts/' + this.postId + '/categories', {categories: value}).then((response) => {
                show_stack_success('Kategori terupdate!', response)
            }, function (response) {
                show_stack_error('Gagal update kategori!', response)
            });
        },
        deletePost (post) {
            let self = this
            swal({
                title: 'Anda yakin?',
                text: 'Postingan akan terhapus selamanya!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus ini!',
                cancelButtonText: 'Tidak',
            }).then(function () {
                self.$http.delete('/api/posts/' + post.hashid, post).then(function (response) {
                    self.$router.go('/posts')
                    swal(
                            'Terhapus!',
                            'Postingan anda telah terhapus.',
                            'success'
                    );
                }, function (response) {
                    show_stack_error('Deletion of post went wrong.', response)
                })
            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay', 'close', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                            'Tidak jadi',
                            'Postingan anda aman :)',
                            'error'
                    );
                }
            });
        }
    },
    computed: {
        isPublished: function () {
            if (this.post.status !== 'approved') {
                return true
            } else {
                return false
            }
        }
    }
}
</script>
