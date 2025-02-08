<x-app-layout>

    <div class="">
        <section class="px-6 py-12 text-[#800000] text-center ">
            <h1 class=" m-5 font-bold text-xl">Welcome to Edufund Africa</h1>
            <p class=" m-5">Empowering education across Africa through technology and community initiatives.</p>
        </section>
        <section class="flex flex-wrap justify-center gap-5 p-5 max-w-5xl mx-auto">
            <div class="bg-white rounded-lg text-center shadow-md overflow-hidden p-4 transition-transform duration-300 hover:shadow-lg hover:scale-105 w-[200px]">
                <img src="assets/images/qna.jpg" alt="Q&amp;A" class="w-full h-[120px] object-cover rounded-md block mx-auto mb-2">
                <a href="{{ route('quiz-dashboard') }}" class="block text-lg font-bold text-maroon no-underline">Question &amp; Answer</a>
            </div>
            <div class="bg-white rounded-lg text-center shadow-md overflow-hidden p-4 transition-transform duration-300 hover:shadow-lg hover:scale-105 w-[200px]">
                <img src="assets/images/past-questions.jpg" alt="Past Questions" class="w-full h-[120px] object-cover rounded-md block mx-auto mb-2">
                <a href="{{ route('past-questions') }}" class="block text-lg font-bold text-maroon no-underline">Past Questions</a>
            </div>
            <div class="bg-white rounded-lg text-center shadow-md overflow-hidden p-4 transition-transform duration-300 hover:shadow-lg hover:scale-105 w-[200px]">
                <img src="assets/images/tutorials.jpg" alt="Tutorials" class="w-full h-[120px] object-cover rounded-md block mx-auto mb-2">
                <a href="tutorials.html" class="block text-lg font-bold text-maroon no-underline">Tutorials</a>
            </div>
        </section>

    </div>




</x-app-layout>