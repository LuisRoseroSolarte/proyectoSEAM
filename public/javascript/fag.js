// Funcionalidad de búsqueda en FAQ
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearch');
    const accordionItems = document.querySelectorAll('.accordion-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            accordionItems.forEach(item => {
                const questionText = item.querySelector('.question-text').textContent.toLowerCase();
                const answerText = item.querySelector('.accordion-body').textContent.toLowerCase();

                if (searchTerm === '') {
                    item.style.display = 'block';
                } else if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    item.style.display = 'block';
                    // Opcional: highlight del término buscado
                } else {
                    item.style.display = 'none';
                }
            });

            // Mensaje si no hay resultados
            const visibleItems = Array.from(accordionItems).filter(item => item.style.display !== 'none');
            const accordion = document.querySelector('.accordion');
            let noResultsMsg = document.getElementById('noResultsMessage');

            if (visibleItems.length === 0 && searchTerm !== '') {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'no-results';
                    noResultsMsg.innerHTML = `
                        <p>😕 No encontramos resultados para "<strong>${searchTerm}</strong>"</p>
                        <p>Intenta con otros términos o <a href="contacto.html">contáctanos directamente</a></p>
                    `;
                    accordion.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }
});