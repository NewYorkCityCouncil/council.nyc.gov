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



// logic for accordion component
document.addEventListener('DOMContentLoaded', function () {
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');

        header.addEventListener('click', () => {
            const content = item.querySelector('.accordion-content');

            // Toggle visibility
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });
});
