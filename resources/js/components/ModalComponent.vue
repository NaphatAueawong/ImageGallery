<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-body">
                    <img v-bind:src=" '/storage/' + path + filename">
                    <div>
                        <button class="modal-default-button btn btn-primary" v-on:click="$emit('close')">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:[
            'path', 'filename'
        ],

        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style>

    .modal-mask {
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 700px;
        margin: auto;
        padding: 20px 30px;
        background-color: #fff;
    }

    .modal-body {
          margin: 20px;
      }

    .modal-default-button {
        margin-top: 10px;
        float: right;
    }

    img {
        max-width: 500px;
        max-height: 500px;
    }

</style>