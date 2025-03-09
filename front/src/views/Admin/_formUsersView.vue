<script setup></script>
<template>
    <div class="bg-white shadow sm:rounded-lg border border-gray-200">
        <form action="verifAjoutCompet.php" method="post" class="px-4 py-5 sm:px-6">
            <div class="mb-6">
                <label for="compet_nom" class="block mb-2 text-sm font-medium text-gray-900">Nom de la compétition</label>
                <input type="text" id="compet_nom" name="compet_nom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nom de la compétiton" required="">
            </div>
            <div class="mb-6">
                <label for="compet_type" class="block mb-2 text-sm font-medium text-gray-900">Type de compétition</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="compet_type" id="compet_type" required="">
                    <option value="" selected="">Choisissez une option</option>
                    <option value="Adultes">Adultes</option>
                    <option value="Edt">Ecole de tir</option>
                </select>
            </div>
            <span class="block mb-2 text-sm font-medium text-gray-900">Dates de la compétition</span>

            <div id="date-range-picker" date-rangepicker="" class="flex items-center mb-6">
                <span class="mx-4 text-gray-500 text-sm">Du</span>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"></path>
                        </svg>
                    </div>
                    <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 datepicker-input" placeholder="Date de début" required="">
                </div>
                <span class="mx-4 text-gray-500 text-sm">au</span>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"></path>
                        </svg>
                    </div>
                    <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 datepicker-input" placeholder="Date de fin" required="">
                </div>
            </div>
            <div class="mb-6">
                <label for="compet_adrs" class="block mb-2 text-sm font-medium text-gray-900">Adresse de la compétition</label>
                <select name="compet_adrs" id="compet_adrs" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required="">
                    <option selected="" value="">Choisissez une adresse</option>
                    <option value="0">OLYMP. CLUB GIFFOIS</option><option value="1">L.C. VILLEMOISSON</option><option value="2">C.T. MONTGERON</option><option value="3">A.C.T.E.</option><option value="4">C.T.S. IGNY</option><option value="5">C.T. AILES DE BOISSY</option><option value="6">ASCEA-GR SECTION TIR</option><option value="7">S.T SAPEURS-POMPIERS</option><option value="8">ATICRE</option><option value="9">ARQUEBUSIERS CERNY</option><option value="10">BAC - TIR</option><option value="11">C.O. COURCOURONNES</option><option value="12">TIR DU HUREPOIX</option><option value="13">A.S. D'OLLAINVILLE</option><option value="14">CT COUDRAY MONTCEAUX</option><option value="15">CLUB IBM PARIS</option><option value="16">E.G. ETAMPOISE</option><option value="17">ARQUEBUSE DE WISSOUS</option><option value="18">CIBLE SOISY/S/SEINE</option><option value="19">LA CIBLE DE VILLEBON</option><option value="20">C.T. PALAISEAU</option><option value="21">C.S.P. MONTLHERY</option><option value="22">A.S.C.E.A.  SACLAY</option><option value="23">S.T. ARPAJONNAIS</option><option value="24">ETRECHY E.T.C.</option><option value="25">ETRECHY TIR SPORTIF</option><option value="26">LE C.T. DE WISSOUS</option><option value="27">C.T. PARIS SACLAY</option><option value="28">A.S.T. BEL-AIR</option>                                        </select>
            </div>
            <div class="mt-4">
                <button type="submit" class="rounded-md text-sm px-4 py-2 shadow-sm font-normal text-white bg-black transition duration-200">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</template>