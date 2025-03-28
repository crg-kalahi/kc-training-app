<template>
    <div class="min-h-screen bg-gradient-to-r from-blue-500 to-teal-400 flex flex-col justify-center items-center text-white" 
    style="background-image: url('/images/header.png');background-size: cover; background-repeat: no-repeat;background-position: center;">
    <header class="w-4/5 p-10 flex justify-between items-center">
  <!-- Logo and Title -->
  <div class="flex items-center space-x-4">
    <img src="images/trainlogo1.png" alt="T.R.A.I.N Logo" class="h-10 w-10 object-contain">
    <h1 class="text-3xl font-bold">T.R.A.I.N</h1>
  </div>

  <!-- Navigation -->
  <nav class="flex space-x-4">
    <a href="#features" class="hover:underline" @click.prevent="scrollToSection('features')">Features</a>
    <a href="#about" class="hover:underline" @click.prevent="scrollToSection('about')">About</a>
    <a href="#team" class="hover:underline" @click.prevent="scrollToSection('team')">Team</a>
    <a href="#contact" class="hover:underline" @click.prevent="scrollToSection('contact')">Contact</a>
    <a href="#developers" class="hover:underline" @click.prevent="scrollToSection('developers')">Developers</a>
  </nav>
</header>
  
      <main class="flex-1 flex flex-col justify-center items-center text-center p-10" >
        <h2 class="text-5xl font-extrabold mb-4">Training Resources and Information Network</h2>
        <p class="text-xl max-w-2xl mb-6">
          Training Management Application aimed at streamlining and optimizing capacity-building process.
        </p>
        <a href="/login" class="bg-white text-blue-500 px-6 py-3 rounded-full shadow-lg font-bold hover:bg-blue-100 transition">
          Get Started
        </a>
      </main>
  
      <section id="features" class="w-full bg-white text-gray-800 py-16"  >
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 px-4">
          <div class="text-center p-6 border rounded-lg shadow-sm hover:shadow-lg transition bg-white">
            <div class="text-4xl mb-4">🚀</div>
            <h3 class="text-2xl font-semibold mb-2">Efficiency in Training Management</h3>
            <p>The system automates administrative tasks of the CBU. Specifically, the dashboard provides users with valuable insights into the training program, including the total number of training conducted within the past 12 months, a calendar view of upcoming activities, and a summary of training outcomes. This information enables users to effectively monitor and evaluate the training program's progress, insights, and performance.
            </p>
          </div>
          <div class="text-center p-6 border rounded-lg shadow-sm hover:shadow-lg transition bg-white">
            <div class="text-4xl mb-4">🎯</div>
            <h3 class="text-2xl font-semibold mb-2">Streamlined Process</h3>
            <p>Apart from automation of administrative tasks, the system also automatically sends emails containing detailed results and feedback to resource persons (rp) as part of the evaluation process. This feature ensures that RPs have timely access to valuable insights regarding their training sessions, including participant ratings, comments, and suggestions for improvement. </p>
          </div>
          <div class="text-center p-6 border rounded-lg shadow-sm hover:shadow-lg transition bg-white">
            <div class="text-4xl mb-4">✨</div>
            <h3 class="text-2xl font-semibold mb-2">Centralized Content Repository</h3>
            <p>Trainers can store information and materials within the system, facilitating the easy management of training resources and ensuring that all information remains current and standardized across various training sessions. 
            </p>
          </div>
        </div>
      </section>
  
      <section id="about" class="w-full bg-gray-100 text-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold text-indigo-600 mb-6 text-center">About TRAIN</h1>
            <p class="text-lg leading-relaxed mb-6">
            The <strong class="font-semibold text-indigo-600">Training Resources and Information Network (TRAIN)</strong> system is a centralized platform designed to streamline and enhance the management of learning and development interventions under the KALAHI-CIDSS program. Developed in 2023 by the Capacity Building Unit (CBU) in collaboration with IT specialists, TRAIN was conceptualized in response to the loss of vital training data caused by a cyberattack in 2022.
            </p>
            <p class="text-lg leading-relaxed mb-6">
            Initially piloted in March 2023, the system has since been used in 84 training activities, delivering significant benefits, including the elimination of paper forms, streamlined data collection and analysis, and faster turnaround for insights and decision-making. TRAIN also resolves challenges in retrieving critical data, such as participant information and sex-disaggregated statistics, ensuring secure and efficient data storage.
            </p>
            
            <h2 class="text-3xl font-semibold text-indigo-600 mb-6 text-center">Key Features of TRAIN</h2>
            <ul class="space-y-6 mb-6">
                <li class="flex items-center text-lg">
                    <span class="text-indigo-600 mr-4">📊</span>
                    <span><strong>Dashboard:</strong> Summarizes cumulative data, including total training sessions, participant demographics, and internal/external participant counts.</span>
                </li>
                <li class="flex items-center text-lg">
                    <span class="text-indigo-600 mr-4">📚</span>
                    <span><strong>Training Management:</strong> Records and organizes training details, such as titles, venues, facilitators, topics, and evaluation forms, with options for editing.</span>
                </li>
                <li class="flex items-center text-lg">
                    <span class="text-indigo-600 mr-4">📝</span>
                    <span><strong>Master Records:</strong> Provides essential references for analyzing training effectiveness and outcomes.</span>
                </li>
                <li class="flex items-center text-lg">
                    <span class="text-indigo-600 mr-4">🔗</span>
                    <span><strong>QR Code Integration:</strong> Enables online and paperless training evaluations for participants.</span>
                </li>
            </ul>

            <h2 class="text-3xl font-semibold text-indigo-600 mb-6 text-center">Future Enhancements</h2>
            <p class="text-lg leading-relaxed mb-6">
            The TRAIN system will continue to evolve with support from the RPMO and Knowledge Management Team. Planned improvements include enhanced data visualization on the dashboard, expanded repository features for storing training materials, and more robust tools for analysis and reporting. These upgrades aim to ensure consistent, high-quality training delivery to better achieve capacity-building goals.
            </p>
        </div>
        </section>

  
        <section id="team" class="w-full bg-white text-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-3xl font-bold mb-6">Team</h3>
            
            <!-- Team members grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <!-- Team member 1 -->
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                <img src="/images/teams/robelene.jpg" alt="Team Member 1" class="w-32 h-32 mx-auto mb-4 rounded-full object-cover">
                <h4 class="text-xl font-semibold mb-2">Robelene C. Becerro</h4>
                <p class="text-gray-600 mb-4">Training Specialist IV</p>
                <p class="text-gray-500">Capacity Building Unit KALAHI CIDSS</p>
            </div>

            <!-- Team member 2 -->
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                <img src="/images/teams/eds.jpg" alt="Team Member 2" class="w-32 h-32 mx-auto mb-4 rounded-full object-cover">
                <h4 class="text-xl font-semibold mb-2">Edsariane P. Cupin</h4>
                <p class="text-gray-600 mb-4">Training Specialist III</p>
                <p class="text-gray-500">Capacity Building Unit KALAHI CIDSS</p>
            </div>

            <!-- Team member 3 -->
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                <img src="/images/teams/rin.jpg" alt="Team Member 3" class="w-32 h-32 mx-auto mb-4 rounded-full object-cover">
                <h4 class="text-xl font-semibold mb-2">Victorino A. Sistoso</h4>
                <p class="text-gray-600 mb-4">Training Specialist III</p>
                <p class="text-gray-500">Capacity Building Unit KALAHI CIDSS</p>
            </div>
            </div>
        </div>
        </section>
  
        <section id="contact" class="w-full bg-gray-100 text-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-3xl font-bold mb-6">Contact Us</h3>
            <p class="text-lg mb-8">Have a question or need support? We're here to help! Reach out to us anytime.</p>

            <!-- Contact Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h4 class="text-2xl font-semibold mb-4">Send us a message</h4>
                <form action="#" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium">Your Name</label>
                    <input type="text" id="name" name="name" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your name">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium">Your Email</label>
                    <input type="email" id="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email">
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium">Your Message</label>
                    <textarea id="message" name="message" required rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your message here"></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h4 class="text-2xl font-semibold mb-4">Get in Touch</h4>
                <p class="text-lg mb-4">Prefer to contact us directly? You can reach us via email:</p>
                <div class="flex items-center space-x-2 text-lg font-medium text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12l-6-6m0 0l-6 6m6-6v12"></path></svg>
                <a href="mailto:kalahicapbuildcaraga@gmail.com" class="hover:underline">kalahicapbuildcaraga@gmail.com</a>
                </div>
            </div>
            </div>

            <!-- Optional: A map or location -->
            <div class="mt-12">
            <h4 class="text-2xl font-semibold text-center mb-4">Our Location</h4>
            <div class="aspect-w-16 aspect-h-9">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1970.6542694429425!2d125.53467470431444!3d8.943690055103659!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3301c04fea75abc9%3A0xc04cf3f7a098d4e7!2sDepartment%20of%20Social%20Welfare%20and%20Development%20(DSWD)%20-%20Field%20Office%20Caraga!5e0!3m2!1sen!2sph!4v1737950650513!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full border-0 rounded-lg shadow-lg" ></iframe>
            </div>
            </div>
        </div>
        </section>
  
      <section id="developers" class="w-full bg-white text-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-3xl font-bold mb-6">Knowledge Management</h3>
            
            <!-- User Avatars -->
            <div class="flex justify-center gap-8">
            <!-- User 1 -->
            <div class="flex flex-col items-center">
                <img src="/images/teams/alvin.jpg" alt="User 1" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h4 class="text-xl font-semibold">Alvin M. Mapalo </h4>
                <p class="text-gray-600">Writer - SMO III</p>
            </div>

            <!-- User 2 -->
            <div class="flex flex-col items-center">
                <img src="/images/teams/rin.jpg" alt="User 2" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h4 class="text-xl font-semibold"> Victorino A. Sistoso </h4>
                <p class="text-gray-600">Writer - TS III</p>
            </div>

            <!-- User 3 -->
            <div class="flex flex-col items-center">
                <img src="/images/teams/jade.png" alt="User 3" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h4 class="text-xl font-semibold">Dioame Jade C. Rendon</h4>
                <p class="text-gray-600">Developer - CP III</p>
            </div>


               <!-- User 3 -->
               <div class="flex flex-col items-center">
                <img src="/images/teams/jolan.jpg" alt="User 3" class="w-24 h-24 rounded-full mb-4 object-cover">
                <h4 class="text-xl font-semibold">Jolan D. Owang</h4>
                <p class="text-gray-600">Former Developer</p>
            </div>


            </div>
        </div>
        </section>
  
      <footer class="w-full bg-gray-800 text-gray-400 py-8 text-center">
        <p>&copy; TRAIN. All rights reserved.</p>
        <div class="mt-4 flex justify-center space-x-4">
          <a href="#" class="hover:text-white">Privacy Policy</a>
          <a href="#" class="hover:text-white">Terms of Service</a>
        </div>
      </footer>
    </div>
  </template>
  
  <script>
  export default {
    name: "LandingPage",
    methods: {
      scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
          const headerOffset = 80; // Adjust this value to match your header height
          const elementPosition = section.getBoundingClientRect().top + window.scrollY;
          const offsetPosition = elementPosition - headerOffset;
  
          window.scrollTo({
            top: offsetPosition,
            behavior: "smooth",
          });
        }
      },
    },
  };
  </script>
  
  <style>
  body {
    margin: 0;
    font-family: 'Inter', sans-serif;
  }


  
  </style>
  