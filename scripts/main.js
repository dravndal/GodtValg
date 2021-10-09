/* main.js laget av Kevin André Torgrimsen Nordli. Sist endret 02.06.2021 av Kevin André Torgrimsen Nordli. */

// Deklarering av variabler for henting av elementer i DOM-treet
const navbarButton = document.querySelector(".navbar-button"); // Hent (det første) elementet med klassen navbar-button
const navbarList = document.querySelector(".navbar-list");
const navbarMenuBackground = document.querySelector(".navbar-menu-background");
const navbarLinks = document.querySelectorAll(".navbar-link");
const navbarRight = document.querySelector(".navbar-right");

const candidateList = document.querySelector(".candidate-list");
const candidateButtonPrevious = document.querySelector(".candidate-button-previous");
const candidateButtonNext = document.querySelector(".candidate-button-next");

const picture = document.querySelector("#picture"); // Hent elementet med id-en picture
const pictureInput = document.querySelector(".picture-input");
const pictureInputImage = document.querySelector("#picture-input-image");
const pictureInputText = document.querySelector("#picture-input-text");
const changePictureIndicator = document.querySelector(".change-picture-indicator");

const birthdate = document.querySelector("#birthdate");

const startDate = document.querySelector("#start-date");
const endDate = document.querySelector("#end-date");

/* Flatpickr oppsett */
flatpickr.localize(flatpickr.l10ns.no); // Bruk norsk lokalisering
flatpickr.l10ns.default.firstDayOfWeek = 1; // Sett første dagen i uka til å være mandag

const flatpickrDateConfig = { // Objekt med konfigurasjoner for dato uten timer og minutter
  dateFormat: "Y-m-d", // Format for databasen
  altInput: true, // Tillat alternativ input for visning til brukeren
  altFormat: "j. F Y" // Format for visning til brukeren
}

const flatpickrDatetimeConfig = { // Objekt med konfigurasjoner for dato med timer og minutter
  enableTime: true, // Tillat timer, minutter og sekunder
  time_24hr: true, // Bruk 24-timers klokkeslett fremfor 12-timer
  dateFormat: "Y-m-d H:i:S",
  altInput: true,
  altFormat: "j. F Y \\k\\l. H:i"
}

if (birthdate) {
  const birthdateConfig = flatpickrDateConfig; // Sett birtdateConfig som en pointer til flatpickrDateConfig (jeg utfører ingen kopiering siden ingen av property-ene skal muteres, dette aliaset er kun for ryddighet og DRY kode)
  birthdateConfig.maxDate = new Date(); // Tillater kun datoer bakover i tid. Siden det ikke er spesifisert noen minstealder i oppgaven så gjør vi det ikke her heller
  birthdateConfig.minDate = new Date(new Date().setFullYear(new Date().getFullYear() - 120)); // Tillater kun fødselsdatoer som går tilbake 120 år. I dette tilfellet (2021) ville det blitt 1901
  const birthdatePicker = flatpickr(birthdate, flatpickrDateConfig);
}

if (startDate && endDate) {
  const startDateConfig = flatpickrDatetimeConfig;
  const endDateConfig = flatpickrDatetimeConfig;
  startDateConfig.minDate = new Date();
  let startDatePicker = flatpickr(startDate, startDateConfig);
  let endDatePicker = flatpickr(endDate, endDateConfig);
  startDatePicker.config.onChange.push(function(selectedDates) {
    endDatePicker.set("minDate", selectedDates[0]); // Når datoen endres, så skal minstedatoen til sluttdatoen være den datoen som velges, slik at brukeren ikke kan velge en sluttdato som er før startdatoen
  });
}

// Registrering av event listeners
if (navbarButton && navbarList && navbarMenuBackground) { // Sørg for å kun legge til event listeners om variabelet ikke er undefined
  navbarButton.addEventListener("click", () => {
    navbarList.classList.toggle("navbar-list-open");
    navbarMenuBackground.classList.toggle("background-open");
  });
  navbarMenuBackground.addEventListener("click", () => {
    navbarList.classList.remove("navbar-list-open");
    navbarMenuBackground.classList.toggle("background-open");
  })
}

// Funksjonalitet for next og previous-knappene til avstemming-siden
if (candidateList) {
  const candidateListItemWidth = document.querySelector(".candidate-list-item").clientWidth;

  candidateButtonPrevious.addEventListener('click', () => candidateList.scrollBy({ left: -candidateListItemWidth, top: 0, behavior: 'smooth' }));
  candidateButtonNext.addEventListener('click', () => candidateList.scrollBy({ left: candidateListItemWidth, top: 0, behavior: 'smooth' }));
}


// Viser en preview av bildet du laster opp
if (picture && pictureInput) {
  picture.addEventListener("change", function() {
    const file = picture.files[0]; // Hent fila
  if (file) {
    pictureInputImage.setAttribute("src", ""); // Fjern bildet som allerede er der
    if (pictureInputText) {
      pictureInputText.style.display = "none"; // Fjern teksten
    }
    /* Diverse styling */
    pictureInputImage.style.width = "100%";
    pictureInputImage.style.transition = "none";
    pictureInputImage.style.height = "100%";
    pictureInputImage.style.filter = "none";
    pictureInputImage.style.margin = "0";
    pictureInputImage.style.borderRadius = "50%";
    pictureInput.style.padding = "0";
    changePictureIndicator.style.display = "flex";

    const fileReader = new FileReader();

    fileReader.addEventListener("load", function () { // Når filen lastes inn
      pictureInputImage.setAttribute("src", this.result); // Sett img-taggen til å vise bildet valgt av brukeren
    });
    fileReader.readAsDataURL(file);
  }
  });
}
