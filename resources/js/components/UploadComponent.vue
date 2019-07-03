<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Gallery
            </div>
            <div class="card-body">
                <div class="uploader"
                     @drop="onDrop">
                    <input class="file-input" type="file" id="files" ref="files" multiple v-on:change="handleFiles()"/>
                    <i class="fa fa-cloud-upload"></i>
                    <p>Drop files here or click to choose files...</p>
                </div>

                <div class="row">
                    <div class="col-md-4" v-for="(image, key) in images">
                        <div v-if="image.status === 'uploaded' ">
                            <div class="image-thumbnail">

                                <img v-bind:src=" '/storage/' + image.path + image.filename">
                                <div class="preview-del-icon">
                                    <button class="btn btn-primary" v-on:click.prevent="toggleModal(key)"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-danger" v-on:click.prevent="removeFile(key, image.id)"><i class="fa fa-trash"></i></button>
                                </div>

                                <modal-component v-show="image.showModal"
                                                 v-on:close="toggleModal(key)"
                                                 :path="image.path"
                                                 :filename="image.filename">
                                </modal-component>

                            </div>
                        </div>

                        <div v-else>
                            <div class="card">
                                <div class="card-body">
                                    <div v-if="image.errors.length > 0">
                                        <div v-for="error in image.errors">
                                            <p>this picture error : {{error}}</p>
                                        </div>
                                        <button  v-on:click.prevent="removeErrorFile(key)" >Delete</button>
                                    </div>
                                    <div v-else>
                                        <p>progress : {{image.progress}}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</template>

<script>

    export default {

        data(){
            return {
                images: [],
            }
        },

        methods:{
            onDrop(e) {
                e.preventDefault();
                this.sendReq(e.dataTransfer.files);
            },

            handleFiles(){
                this.sendReq(this.$refs.files.files);
            },

            removeErrorFile(key){
                this.images.splice(key, 1);
            },

            removeFile(key, imageId){
                this.images.splice(key, 1);
                axios.delete(this.endpoint + '/' + imageId).then(res => {
                    console.log(res.data.message);
                });
            },

            toggleModal(key) {
                if(this.images[key].showModal === false){
                    this.images[key].showModal = true;
                }else{
                    this.images[key].showModal = false;
                }

            },

            sendReq(selectedImages){

                for (let selectedImage of selectedImages){

                    let self = this.images.push({
                        'id': '',
                        'filename' : 'init',
                        'path' : '',
                        'status' : 'notUpload',
                        'progress' : 0,
                        'errors' : [],
                        'showModal' : false,
                    });

                    console.log(this.images);

                    if(this.validateSelectedImage(self, selectedImage)){

                        console.log('after validate is true ok');

                        let imageUpdate = this.images[self-1];

                        let formData = new FormData();
                        formData.append('file', selectedImage);

                        axios.post(this.endpoint, formData, {

                            onUploadProgress: e => {
                                imageUpdate.progress = Math.round(e.loaded * 100 / e.total);
                            }

                        }).then(res => {

                            this.$refs.files.value = '';
                            imageUpdate.id = res.data.image.id;
                            imageUpdate.filename = res.data.image.filename;
                            imageUpdate.path = res.data.image.path;
                            imageUpdate.status = 'uploaded';
                            imageUpdate.progress = 100;
                            console.log(res.data.message);

                        }).catch(err => {
                            console.log(err.response.data.message);
                        });
                    }
                }
            },

            validateSelectedImage(self, selectedImage){

                let validateImage = this.images[self-1];
                console.log(validateImage);

                if(selectedImage.size > 20000000){
                    validateImage.errors.push('The image size must not more than 20 MB');
                }
                if(selectedImage.type !== "image/jpeg" && selectedImage.type !== "image/jpg" && selectedImage.type !== "image/png"){
                    validateImage.errors.push('The image type must be JPEG, JPG, PNG');
                }

                if(validateImage.errors.length > 0){
                    return false;
                }else{
                    return true;
                }
            }
        },

        computed: {
            endpoint(){
                return '/uploadImage';
            },
        },

        mounted() {
            console.log('Component mounted.');
            console.log(this.images);
            axios.get(this.endpoint).then(res => {
                let imagesRes = res.data.images;

                if (imagesRes.length > 0) {
                    for(let image of imagesRes){
                        this.images.push({
                            'id': image.id,
                            'filename' : image.filename,
                            'path' : image.path,
                            'status' : 'uploaded',
                            'progress' : 100,
                            'showModal' : false
                            // 'errors' : [],
                        });
                    }
                }
                console.log(this.images);
                console.log(res.data.message);
            });
        }
    }

</script>

<style>
    .uploader {
        min-height: 200px;
        background: #ffffff;
        color: #969696;
        margin-bottom: 25px;
        border: 3px dashed #b5b5b5;
        font-size: 20px;
        position: relative;
        cursor: pointer;
    }

    .uploader i{
        width: 100%;
        padding-top: 40px;
        text-align: center;
        font-size: 70px;
    }

    .uploader p{
        text-align: center;
        font-size: 20px;
    }

    .file-input{
        opacity: 0;
        width: 100%;
        height: 200px;
        position: absolute;
        cursor: pointer;
    }

    .image-thumbnail {
        position: relative;
        text-align: center;
        margin-bottom: 25px;

    }

    .image-thumbnail:hover .preview-del-icon{
        display: block;
    }

    img {
        width: 250px;
        height: 250px;
    }

    .preview-del-icon{
        position: absolute;
        top:100px;
        left:130px;
        display: none;
    }

</style>

//header Xrequested with value xml request.