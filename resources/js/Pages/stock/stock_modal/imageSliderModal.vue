<template>



 <div  class="backdrop bg-black overflow-y-auto" @click.self="closeModal">
<Loading v-model:active="isLoading"
:can-cancel="true"
:is-full-page="fullPage"/>
<div class="modal">
     
<!-- Container for the image gallery -->
<div class="object-scale-down">
  <button @click="closeModal" type="button" 
    class="mt-4 mb-2 w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
    Close
    </button>
  <!-- Full-width images with number text -->
  <div class="mySlides">
    <div class="numbertext">1 / 3</div>
      <img :src="record.img" style="width:100%">
  </div>

  <div class="mySlides">
    <div class="numbertext">2 / 3</div>
      <img :src="record.img_two" style="width:100%">
  </div>

  <div class="mySlides">
    <div class="numbertext">3 / 3</div>
      <img :src="record.img_three" style="width:100%">
  </div>


  <!-- Next and previous buttons -->
  <a class="prev" @click="plusSlides(-1)">&#10094;</a>
  <a class="next" @click="plusSlides(1)">&#10095;</a>

  <!-- Thumbnail images -->
  <div class="grid grid-cols-3 gap-4 mt-2">
    <!-- Thumbnial image 1 -->
    <div>
        <div :id="'firstPreviewImageUpdate_' + record.id" x-show="!firstImagePreviewUpdate" class="text-center">
            <template v-if="firstImagePreviewUpdate && record.id == firstCurrentPreviewUpdateId" >
                <img :src="firstImagePreviewUpdate" class="inline-block shadow demo cursor w-3/4" @click="currentSlide(1)"
                alt="Image 1"
                >
            </template>
            <template v-else>
                <img  @click="currentSlide(1)" 
                :src="record.img" 
                class="inline-block shadow demo cursor w-22 h-16 rounded-full"
                alt="Image 1"
                >
            </template>                                      
        </div>
        <div class="mt-1 text-center">
                <button  
                 v-if="getAuth.isFirstLevelUser|| getAuth.isSecondLevelUser || getAuth.isThirdLevelUser" 
                class=" px-1  m-1 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none">
                    <input type="file" @change="modalThumbnailImageChanged($event,record.id,1)"  
                    :data-index="record.id"
                    :id="'changeModalImage_' + record.id"
                    accept=".jpg,.jpeg,.png"
                    class="hidden"/>
                    <label :for="'changeModalImage_' + record.id">Change</label>
                </button>
                <button v-if="record.id == firstCurrentPreviewUpdateId" class="px-3  m-1 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"> 
                    <input type="submit" @click="saveImage(record.id)"  :id="'saveUpdateImage_' + record.id" class="hidden"/>
                <label :for="'saveUpdateImage_' + record.id">Save</label>
                </button>   
                <button v-if="record.id == firstCurrentPreviewUpdateId" class="px-3  m-1 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-blue-700  focus:outline-none"> 
                    <input type="button" @click="firstImagePreviewUpdate = null; this.firstCurrentPreviewUpdateId= null;"  :id="'cancelUpdateImage_' + record.id" class="hidden"/>
                <label :for="'cancelUpdateImage_' + record.id">Cancel</label>
                </button>   
            </div>
    </div>
    <!-- Thumbnial image 2 -->
    <div>
        <div :id="'secondPreviewImageUpdate_' + record.id" x-show="!secondImagePreviewUpdate" class="text-center">
            <template v-if="secondImagePreviewUpdate && record.id == secondCurrentPreviewUpdateId" >
                <img :src="secondImagePreviewUpdate" class="inline-block shadow demo cursor w-3/4" @click="currentSlide(2)"
                alt="Image 2"
                >
            </template>
            <template v-else>
                <img  @click="currentSlide(2)" 
                :src="record.img_two" 
                class="inline-block shadow demo cursor w-22 h-16 rounded-full"
                alt="Image 1"
                >
            </template>                                      
        </div>
        <div class="mt-1 text-center">
                <button  
                 v-if="getAuth.isFirstLevelUser|| getAuth.isSecondLevelUser" 
                class=" px-1 m-1 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none">
                    <input type="file" @change="modalThumbnailImageChanged($event,record.id,2)"  
                    :data-index="record.id"
                    :id="'changeModalImage2_' + record.id"
                    accept=".jpg,.jpeg,.png"
                    class="hidden"/>
                    <label :for="'changeModalImage2_' + record.id">Change</label>
                </button>
                <button  v-if="record.id == secondCurrentPreviewUpdateId"  class="px-3 m-1 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"> 
                    <input type="submit" @click="saveModalImage(record.id,2)"  :id="'saveUpdateImage_' + record.id" class="hidden"/>
                <label :for="'saveUpdateImage_' + record.id">Save</label>
                </button>   
                <button v-if="record.id == secondCurrentPreviewUpdateId"  class="px-3 m-1 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-blue-700  focus:outline-none"> 
                    <input type="button" @click="secondImagePreviewUpdate = null; this.secondCurrentPreviewUpdateId= null;"  :id="'cancelUpdateImage_' + record.id" class="hidden"/>
                <label :for="'cancelUpdateImage_' + record.id">Cancel</label>
                </button>   
            </div>
    </div>
    <!-- Thumbnial image 3 -->
    <div>
        <div :id="'secondPreviewImageUpdate_' + record.id" x-show="!secondImagePreviewUpdate" class="text-center">
            <template v-if="thirdImagePreviewUpdate && record.id == thirdCurrentPreviewUpdateId" >
                <img :src="thirdImagePreviewUpdate" class="inline-block shadow demo cursor w-3/4" @click="currentSlide(2)"
                alt="Image 2"
                >
            </template>
            <template v-else>
                <img  @click="currentSlide(3)" 
                :src="record.img_three" 
                class="inline-block shadow demo cursor w-22 h-16 rounded-full"
                alt="Image 1"
                >
            </template>                                      
        </div>

        <div class="mt-1 text-center">
                <button  
                 v-if="getAuth.isFirstLevelUser|| getAuth.isSecondLevelUser" 
                class=" px-1 m-1 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none">
                    <input type="file" @change="modalThumbnailImageChanged($event,record.id,3)"  
                    :data-index="record.id"
                    :id="'changeModalImage3_' + record.id"
                    accept=".jpg,.jpeg,.png"
                    class="hidden"/>
                    <label :for="'changeModalImage3_' + record.id">Change</label>
                </button>
                <button  v-if="record.id == thirdCurrentPreviewUpdateId"  class="px-3 m-1 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"> 
                    <input type="submit" @click="saveModalImage(record.id,3)"  :id="'saveUpdateImage_' + record.id" class="hidden"/>
                <label :for="'saveUpdateImage_' + record.id">Save</label>
                </button>   
                <button v-if="record.id == thirdCurrentPreviewUpdateId"  class="px-3 m-1 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-blue-700  focus:outline-none"> 
                    <input type="button" @click="thirdImagePreviewUpdate = null; this.thirdCurrentPreviewUpdateId= null;"  :id="'cancelUpdateImage_' + record.id" class="hidden"/>
                <label :for="'cancelUpdateImage_' + record.id">Cancel</label>
                </button>   
            </div>
    </div>
    
  </div>
 
</div>

 </div>
    </div>

</template>

<script>
import Loading from 'vue-loading-overlay';
import {mapGetters, mapActions} from 'vuex'
export default {
    props: ['recordId', 'record','table_name'],
    components: {Loading},
    
    computed: {
        ...mapGetters({
         getAuth: 'auth/getAuth'
        }),
    },
    data() {
        return {
             slideIndex :  1,
             //image upload
            image:null,
            imagePreview:null,
            firstImagePreviewUpdate:null,
            secondImagePreviewUpdate:null,
            thirdImagePreviewUpdate:null,
            firstCurrentPreviewUpdateId:null,
            secondCurrentPreviewUpdateId:null,
            thirdCurrentPreviewUpdateId:null,

            //Loading Section
            isLoading: false,
            fullPage: true,  
        }
    },
    methods: {


        // Next/previous controls
        plusSlides(n) {
            this.showSlides(this.slideIndex += n);
        },
        // Thumbnail image controls
        currentSlide(n) {
            this.showSlides(this.slideIndex = n);
        },


        
        showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("demo");
        if (n > slides.length) {this.slideIndex = 1}
        if (n < 1) {this.slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[this.slideIndex-1].style.display = "block";
        dots[this.slideIndex-1].className += " active";
        },


        

    imageSelected(e) {
        // this.creating.form.imageIcon = e.target.files[0];
        this.image = e.target.files[0];
        let reader = new FileReader();
        // reader.readAsDataURL( this.creating.form.imageIcon);
        reader.readAsDataURL(this.image);

        reader.onload = e => {
            this.imagePreview = e.target.result;
        }
    },

    modalThumbnailImageChanged(e,id,thumbnailNumber) {

        // this.creating.form.imageIcon = e.target.files[0];
        this.image = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(this.image);

        reader.onload = e => {
            switch(thumbnailNumber){
                case 1:
                    this.firstImagePreviewUpdate = e.target.result;
                    this.firstCurrentPreviewUpdateId= id;
                    break;
                case 2:
                    this.secondImagePreviewUpdate = e.target.result;
                    this.secondCurrentPreviewUpdateId= id;
                    break;
                case 3:
                    this.thirdImagePreviewUpdate = e.target.result;
                    this.thirdCurrentPreviewUpdateId= id;
                    break;

            }
             
        }
    },
    
    saveImage(invoiceId) {
                    
        if(this.image){
            let data = new FormData
            data.append('image', this.image)
            this.isLoading = true
   

        axios.post(`/api/datatable/${this.table_name}/saveImage/${invoiceId}`, data).then((response)=>{
            this.getRecordModal().then(() => {
                this.firstCurrentPreviewUpdateId = null;
                this.imagePreviewUpdate = null;
                this.image =null;
                this.isLoading = false
            })
           
        }).catch((error) => {
            console.log(error)
            
        })
        }  
    },
    saveModalImage(invoiceId,invoiceImgNumber) {
        if(this.image){
            let data = new FormData
            data.append('image', this.image)
        
        let invoiceIDsilderId = invoiceId + '-' + invoiceImgNumber
        this.isLoading = true
        axios.post(`/api/datatable/${this.table_name}/saveImage/${invoiceIDsilderId}`, data).then((response)=>{
                this.getRecordModal()
                this.firstCurrentPreviewUpdateId = null;
                this.imagePreviewUpdate = null;
                this.image =null;
                this.isLoading = false
            })
           
        }  
    },

        closeModal(){
            this.$emit('close')
        },

        getRecordModal(){
            this.$emit('getRecordForSlider')
        }
    },

    
    
    mounted() {
    
        this.showSlides(1)
    },
}
</script>
<style>
* {
  box-sizing: border-box;
}


.modal {
    width: 100%;
    height:100%;
    margin: 30px auto;
    border-radius: 10px;
    background-color: black;
}

.backdrop {
    top:0;
    left:0;
    position: fixed;
    background: rgba(0,0,0,1);
    width:100%;
    height:100%;
    z-index: 9999; 
}

/* Position the image container (needed to position the left and right arrows) */
.container {
  position: relative;
}

/* Hide the images by default */
.mySlides {
  display: none;
}

/* Add a pointer when hovering over the thumbnail images */
.cursor {
  cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
  cursor: pointer;
  position: absolute;
  top: 40%;
  width: auto;
  padding: 16px;
  margin-top: -50px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  border-radius: 0 3px 3px 0;
  user-select: none;
  -webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 20;
}


.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Six columns side by side */
.column {
  float: left;
  width: 30%;
}

/* Add a transparency effect for thumnbail images */
.active,
.demo:hover {
  opacity: 1;
}

</style>