<x-app-layout>
    <div class="">
        <section class="px-6 py-12 text-[#800000] text-center ">
            <h1 class="m-5 font-bold text-2xl">Welcome to Edufund Africa</h1>
            <p class="m-5 text-lg">Empowering education across Africa through technology and community initiatives.</p>
        </section>
        
        <section class="flex flex-wrap justify-center gap-6 p-6 max-w-5xl mx-auto">
            <a href="{{ route('quiz-dashboard') }}" class="block w-[220px]">
                <div class="bg-white rounded-xl text-center shadow-lg overflow-hidden p-5 transition-transform duration-300 hover:shadow-xl hover:scale-105">
                    <img src="assets/images/qna.jpg" alt="Q&amp;A" class="w-full h-[130px] object-cover rounded-lg block mx-auto mb-3">
                    <span class="block text-lg font-semibold text-maroon">Question &amp; Answer</span>
                </div>
            </a>
            
            <a href="{{ route('past-questions') }}" class="block w-[220px]">
                <div class="bg-white rounded-xl text-center shadow-lg overflow-hidden p-5 transition-transform duration-300 hover:shadow-xl hover:scale-105">
                    <img src="assets/images/past-questions.jpg" alt="Past Questions" class="w-full h-[130px] object-cover rounded-lg block mx-auto mb-3">
                    <span class="block text-lg font-semibold text-maroon">Past Questions</span>
                </div>
            </a>
            
            <a href="{{ route('tutorials') }}" class="block w-[220px]">
                <div class="bg-white rounded-xl text-center shadow-lg overflow-hidden p-5 transition-transform duration-300 hover:shadow-xl hover:scale-105">
                    <img src="assets/images/tutorials.jpg" alt="Tutorials" class="w-full h-[130px] object-cover rounded-lg block mx-auto mb-3">
                    <span class="block text-lg font-semibold text-maroon">Tutorials</span>
                </div>
            </a>
            
            <a href="/assets/about.pdf" class="block w-[220px]">
                <div class="bg-white rounded-xl text-center shadow-lg overflow-hidden p-5 transition-transform duration-300 hover:shadow-xl hover:scale-105">
                    <img src="/assets/images/banner.png" alt="About Us" class="w-full h-[130px] object-cover rounded-lg block mx-auto mb-3">
                    <span class="block text-lg font-semibold text-maroon">About Us</span>
                </div>
            </a>
        </section>
    </div>
</x-app-layout>
