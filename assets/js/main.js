// Toggle menu on mobile
document.getElementById("hamburger").addEventListener("click", () => {
  document.getElementById("menu").classList.toggle("active");
});

// Search using Enter key
document.getElementById("search").addEventListener("keyup", function (e) {
  if (e.key === "Enter") {
    searchAndAdd();
  }
});

// Search and add function (add to grid without clearing existing cards)
async function searchAndAdd() {
  const input = document.getElementById("search");
  const query = input.value.trim();
  if (!query) return;

  try {
    const res = await fetch(`https://api.tvmaze.com/search/shows?q=${encodeURIComponent(query)}`);
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    const grid = document.getElementById("movieGrid");

    data.forEach(({ show }) => {
      // Prevent duplicates by show name
      if ([...grid.children].some(card => card.querySelector('h4').innerText === show.name)) {
        return;
      }

      const image = show.image?.medium || 'https://via.placeholder.com/210x295?text=No+Image';
      const summary = show.summary ? show.summary.replace(/<[^>]+>/g, '') : 'No summary available';

      const card = document.createElement("div");
      card.className = "movie-card";
      card.innerHTML = `
        <img src="${image}" alt="${show.name}" />
        <h4>${show.name}</h4>
        <p>${summary}</p>
        <button onclick="removeCard(this)">Remove</button>
      `;
      grid.appendChild(card);
    });
  } catch (error) {
    alert('Failed to fetch movies. Please try again later.');
    console.error(error);
  }
  input.value = ""; // Clear input
}

// Remove a movie card
function removeCard(btn) {
  btn.parentElement.remove();
}

// Frontend form validation (optional enhancement)
document.getElementById('contactForm').addEventListener('submit', function (e) {
  const form = e.target;
  if (!form.checkValidity()) {
    e.preventDefault();
    alert('Please fill in all required fields correctly.');
  }
});
