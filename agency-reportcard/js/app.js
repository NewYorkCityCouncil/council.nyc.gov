$(document).foundation();

// Toggle the rotation of chevrons on dept headers
const deptHeaders = document.querySelectorAll('.dept');
deptHeaders.forEach(function (deptHeader) {
  deptHeader.addEventListener('click', function () {
    const chevron = this.querySelector('.chevron');
    chevron.classList.toggle('rotate');
  });
});

// Select all card elements and tab panels
const cards = document.querySelectorAll('.card');
const tabPanels = document.querySelectorAll('.tabs-panel');
const tabsContent = document.querySelector('.tabs-content');

// Function to remove 'is-active' class from all cards and hide all panels
function resetActiveState() {
  cards.forEach(card => {
    card.classList.remove('is-active');
  });
  tabPanels.forEach(panel => {
    panel.style.display = 'none'; // Hide all tab panels
  });
}

// Add a click event listener to each card
cards.forEach(card => {
  card.addEventListener('click', function (event) {
    event.preventDefault(); // Prevent default anchor behavior (jumping to the href)

    // Get the href value of the clicked card (which corresponds to the tab panel ID)
    const panelId = card.querySelector('a').getAttribute('href');
    const panelElement = document.querySelector(panelId); // Get the panel element

    // Check if the clicked card already has the 'is-active' class
    if (card.classList.contains('is-active')) {

      // If the card is already active, remove 'is-active' and hide the corresponding panel
      card.classList.remove('is-active');
      panelElement.style.display = 'none';

    } else {
      // If the card is not active, reset all cards and panels first
      resetActiveState();

      // Add 'is-active' class to the clicked card
      card.classList.add('is-active');

      // Display the corresponding tab panel
      panelElement.style.display = 'block';

      // Scroll the page to the 'callout' section (specific to the clicked panel)
      const calloutSection = panelElement.querySelector('.callout');
      if (calloutSection) {
        calloutSection.scrollIntoView({ behavior: 'smooth' });
      }
    }

    // Prevent the event from propagating to the document click listener
    event.stopPropagation();
  });
});

// Add a click event listener to the document (to detect clicks outside the card and tab content)
document.addEventListener('click', function (event) {
  // Check if the click is outside both '.tabs-content' and '.card' elements
  const isClickInsideCard = [...cards].some(card => card.contains(event.target));

  if (!isClickInsideCard && !isClickInsideTabContent(event)) {
    // Remove 'is-active' from all cards and hide all panels when clicking outside
    resetActiveState();
  }
});

// Ensure clicking inside the tab content does not affect 'is-active', but still allow buttons to work
tabsContent.addEventListener('click', function (event) {
  // Check if the target is a button or any interactive element for the modals
  const isModalButton = event.target.tagName === 'BUTTON' || event.target.closest('button');

  // If it's a button (for a modal), we allow the default behavior for the button to trigger the modal
  if (!isModalButton) {
    // Stop propagation to ensure 'is-active' isn't affected when clicking inside the tab content
    event.stopPropagation();
  }
});



// modals content

// Select all modals
const modals = document.querySelectorAll('.reveal');

// Add event listeners for the close buttons in each modal
modals.forEach(modal => {
  const closeButton = modal.querySelector('.close-button');
  
  if (closeButton) {
    closeButton.addEventListener('click', function (event) {
      // Use Foundation's internal modal close event without creating new instances
      $(modal).foundation('close');
      
      // Prevent removing the is-active class from the active card
      event.stopPropagation();
    });
  }

  // Listen for the 'closed.zf.reveal' event to handle the reveal overlay
  $(modal).on('closed.zf.reveal', function () {
    const revealOverlay = document.querySelector('.reveal-overlay');
    if (revealOverlay) {
      revealOverlay.style.display = 'none';
    }
  });
});

// Close the modal when clicking outside it and hide the overlay
document.addEventListener('click', function (event) {
  const revealOverlay = document.querySelector('.reveal-overlay');
  if (revealOverlay && !event.target.closest('.reveal')) {
    revealOverlay.style.display = 'none';
  }
});





