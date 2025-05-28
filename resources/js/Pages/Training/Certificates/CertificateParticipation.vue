<template>
    <div>
        
       <!-- Download Button -->
       <div
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
      >
        <!-- Card Container -->
        <div class="bg-white p-6 rounded-lg shadow-lg text-center w-96">
          <p  v-if="remainingDays > 0" class="text-gray-800 text-2xl mb-4 font-medium">🎉 Hey there, don't miss out!</p>
          <p  v-if="remainingDays > 0" class="text-gray-600 text-lg mb-6">You can download your certificate now.</p>
  
          <!-- New Greeting Message -->
          <p  v-if="remainingDays > 0" class="text-gray-800 text-lg mt-4 font-medium">Greetings from the DSWD Capacity Building Section!</p>
          
          <button
            @click="downloadAsImage"
            class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded text-lg"
            v-if="remainingDays > 0"
          >
            Download Certificate
          </button>
            <div class="text-center text-sm font-medium text-red-600 mb-4 mt-5">
                <p v-if="remainingDays > 0">
                ⚠️ This link will be available for <span class="font-bold">{{ remainingDays }}</span> more {{ remainingDays === 1 ? 'day' : 'days' }}.
                </p>
                <p v-else>
                ⛔ This link has expired.
                </p>
            </div>
        </div>
      </div>
  

      <br><br>
      <!-- Certificate Wrapper -->
      <div ref="certificateRef"
        class="relative mx-auto bg-white shadow-lg font-serif no-select"
        style="width: 1123px; height: 794px; padding: 50px 70px; border: 1px solid #ccc; box-sizing: border-box;"
         v-if="remainingDays > 0"
      >
        <!-- Side ribbon -->
        <div class="absolute left-0 top-0 h-full">
          <img src="/storage/images/new/cert-design.png" alt="Side Ribbon" class="h-full object-cover" />
        </div>


        <div>
          <!-- Header -->
          <div class="flex justify-between items-start mb-6 mt-10">
            <div></div>
            <!-- Left text -->
            <div class="text-left font-[Arial] leading-tight">
              <p class="text-sm font-light">Republic of the Philippines</p>
              <p class="text-sm font-light">Department of Social Welfare and Development</p>
              <p class="text-sm font-light">Field Office Caraga, Capitol Site, Butuan City</p>
            </div>

            <!-- Right logos -->
            <div class="flex space-x-3">
              <img src="/images/dswd.jpg" alt="DSWD Logo" class="h-12" />
              <img src="/images/bp.png" alt="Bagong Pilipinas Logo" class="h-12" />
            </div>
          </div>

          <div class="flex justify-between items-start mb-6 mt-10">
            <div></div>

              <!-- Certificate Content -->
              <div class="text-left mt-6 mb-10 space-y-4">
                <h1 class="text-5xl font-bold font-montserrat">Certificate of Participation</h1>
                <h2 class="text-5xl text-red-600 font-bold font-monotype tracking-wide leading-tight">{{ data.fullname }}</h2>
                <p class="text-lg max-w-3xl">
                  for having successfully participated during the <b>{{ data.title }}</b>,
                  held on {{ training_date }} at <b>{{ data.venue }}</b>.<br><br>
                  Given this <b>{{ training_end_date }}</b>.
                </p>
              </div>
            </div>

          </div>

        <!-- Signature Section -->
        <div class="text-center mt-16 relative">
          <img src="/storage/images/e-sig.png" alt="Signature" class="h-16 mx-auto mb-2 absolute -top-10 left-1/2 transform -translate-x-1/2" />
          <p class="font-bold">MARI-FLOR A. DOLLAGA-LIBANG</p>
          <p class="text-sm">Regional Director</p>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-6 right-6">
          <img src="/storage/images/insignia.jpg" alt="ISO Logo" class="h-12" />
        </div>

        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-[10px] text-gray-500">
          <div class="flex items-center justify-center gap-2">
            <span>DSWD TRAIN | {{ training_id_enc }}</span>
            <QRCodeVue3
             :value="route('cert.verify', {token: training_id_enc, fullname: data.fullname })"
              :dotsOptions="{
                type: 'extra-rounded',
                color: '#26249a',
              }"
              :height="100"
              :width="100"
            />
          </div>
        </div>

      </div>


    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, defineProps } from 'vue'
  import html2canvas from 'html2canvas'
  import QRCodeVue3 from "qrcode-vue3";
  

  const props = defineProps({
        data: Array,
        date: String,
        end_date_raw: String,
        end_date: String,
        training_id_enc: String
    });

  const data = ref(props.data);
  const training_date = ref(props.date);
  const training_end_date = ref(props.end_date);
  const training_id_enc = ref(props.training_id_enc)
  
  const certificateRef = ref(null)
  const remainingDays = ref(0)
  
  const START_DATE = new Date(props.end_date_raw)
  const EXPIRY_DAYS = 5
  
  const calculateRemainingDays = () => {
    const now = new Date()
    const expiryDate = new Date(START_DATE)
    expiryDate.setDate(expiryDate.getDate() + EXPIRY_DAYS)
  
    const timeDiff = expiryDate - now
    remainingDays.value = timeDiff > 0 ? Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) : 0
  }


  
  const downloadAsImage = async () => {
    if (remainingDays.value <= 0) return
  
    const element = certificateRef.value
    if (!element) return
  
    const canvas = await html2canvas(element, {
      useCORS: true,
      scale: 2,
    })
  
    const image = canvas.toDataURL('image/png')
    const link = document.createElement('a')
    link.href = image
    link.download = 'certificate.png'
    link.click()
  }
  
  onMounted(() => {
    calculateRemainingDays()
    // Disable right-click
     document.addEventListener('contextmenu', e => e.preventDefault())
  
     // Disable key shortcuts
     document.addEventListener('keydown', e => {
       if (
         e.key === 'F12' ||
         (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
         (e.ctrlKey && e.key === 'U')
       ) {
         e.preventDefault()
       }
     })
  
    //  Detect DevTools open
     setInterval(() => {
       const threshold = 160
       if (
         window.outerWidth - window.innerWidth > threshold ||
         window.outerHeight - window.innerHeight > threshold
       ) {
         alert("Developer tools are not allowed!")
         window.location.href = "about:blank"
       }
     }, 1000)
  })
  </script>
  
  <style scoped>
  /* Disable text selection */
  .no-select, .no-select * {
    user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    -moz-user-select: none;
  }
  
  /* Remove scrollbars */
  body, html {
    overflow: hidden;
  }
  
  .fixed {
    overflow: hidden;
  }
  </style>
  