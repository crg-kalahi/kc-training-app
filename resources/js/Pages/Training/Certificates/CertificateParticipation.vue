<template>
    <div>
        
  
      <!-- Download Button -->
      <div
        v-if="remainingDays > 0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
      >
        <!-- Card Container -->
        <div class="bg-white p-6 rounded-lg shadow-lg text-center w-96">
          <p class="text-gray-800 text-2xl mb-4 font-medium">🎉 Hey there, don't miss out!</p>
          <p class="text-gray-600 text-lg mb-6">You can download your certificate now.</p>
  
          <!-- New Greeting Message -->
          <p class="text-gray-800 text-lg mt-4 font-medium">Greetings from the DSWD Capacity Building Section!</p>
          
          <button
            @click="downloadAsImage"
            class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded text-lg"
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
      <div ref="certificateRef" class="max-w-4xl mx-auto p-8 border border-gray-300 shadow-lg bg-white relative font-serif no-select mt-30">
        <!-- Side ribbon -->
        <div class="absolute left-0 top-0 h-full w-36">
          <img src="/storage/images/new/cert-design.png" alt="DSWD Logo" class="h-full" />
        </div>
  
        <!-- Header with inline logo -->
        <div class="flex justify-between items-center">
          <div class="text-left space-y-1 ml-40 font-[Arial]">
            <p class="text-sm font-light">Republic of the Philippines</p>
            <p class="text-sm font-light">Department of Social Welfare and Development</p>
            <p class="text-sm font-light">Field Office Caraga, Capitol Site, Butuan City</p>
          </div>
          <div class="flex space-x-4">
            <img src="/images/dswd.jpg" alt="DSWD Logo" class="h-10" />
            <img src="/images/bp.png" alt="Bagong Pilipinas Logo" class="h-10" />
          </div>
        </div>
  
        <!-- Certificate Content -->
        <div class="text-left mt-4 space-y-2 ml-40 mb-5">
          <h1 class="text-4xl font-bold font-montserrat">Certificate of Participation</h1>
          <h2 class="text-5xl text-red-600 font-bold font-monotype">{{data.fullname}}</h2>
          <p class="text-sm max-w-2xl mx-auto">
            for having successfully participated during the <b>{{data.title}}</b>.
            held on {{training_date}} at <br> {{data.venue}}. <br><br> Given this <b>{{training_end_date}}</b>.
          </p>
        </div>
  
        <!-- Signature -->
        <div class="mt-16 text-center leading-tight">
          <div class="relative">
            <img src="/storage/images/e-sig.png" alt="DSWD Signature" class="h-16 mx-auto mt-2 absolute bottom-4 left-1/2 transform -translate-x-1/2" />
            <p class="font-bold m-0 leading-tight">MARI-FLOR A. DOLLAGA-LIBANG</p>
            <p class="text-sm m-0 leading-tight">Regional Director</p>
          </div>
        </div>
  
        <!-- Footer logos -->
        <div class="absolute bottom-4 right-4 flex space-x-2">
          <img src="/storage/images/insignia.jpg" alt="ISO Logo" class="h-10" />
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, defineProps } from 'vue'
  import html2canvas from 'html2canvas'
  

  const props = defineProps({
        data: Array,
        date: String,
        end_date: String
    });

  const data = ref(props.data);
  const training_date = ref(props.date);
  const training_end_date = ref(props.end_date);
  
  const certificateRef = ref(null)
  const remainingDays = ref(0)
  
  const START_DATE = new Date('2025-04-24')
  const EXPIRY_DAYS = 3
  
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
    // document.addEventListener('contextmenu', e => e.preventDefault())
  
    // // Disable key shortcuts
    // document.addEventListener('keydown', e => {
    //   if (
    //     e.key === 'F12' ||
    //     (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
    //     (e.ctrlKey && e.key === 'U')
    //   ) {
    //     e.preventDefault()
    //   }
    // })
  
    // // Detect DevTools open
    // setInterval(() => {
    //   const threshold = 160
    //   if (
    //     window.outerWidth - window.innerWidth > threshold ||
    //     window.outerHeight - window.innerHeight > threshold
    //   ) {
    //     alert("Developer tools are not allowed!")
    //     window.location.href = "about:blank"
    //   }
    // }, 1000)
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
  