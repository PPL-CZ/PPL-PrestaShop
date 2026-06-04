/**
 * PPL Sizes - Multiple package sizes for products
 * Allows defining multiple package dimensions for a single product
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const hiddenInput = document.querySelector('.ppl-sizes-data');
        if (!hiddenInput) return;

        const label = hiddenInput.getAttribute('data-label') || 'Produkt lze případně rozdělit na menší balíčky';

        // Parse existing sizes
        let sizes = [];
        try {
            sizes = JSON.parse(hiddenInput.value) || [];
        } catch (e) {
            sizes = [];
        }

        // Create UI container
        const container = document.createElement('div');
        container.className = 'ppl-sizes-container';
        container.style.marginTop = '10px';
        container.style.marginBottom = '15px';

        // Label
        const labelEl = document.createElement('label');
        labelEl.className = 'form-control-label';
        labelEl.textContent = label;
        container.appendChild(labelEl);

        // Create sizes list
        const sizesList = document.createElement('div');
        sizesList.className = 'ppl-sizes-list';
        sizesList.style.marginTop = '10px';
        container.appendChild(sizesList);

        // Add button
        const addButton = document.createElement('button');
        addButton.type = 'button';
        addButton.className = 'btn btn-primary btn-sm';
        addButton.textContent = '+ Přidat řádek';
        addButton.style.marginTop = '10px';
        container.appendChild(addButton);

        // Insert UI after hidden input
        hiddenInput.parentNode.insertBefore(container, hiddenInput.nextSibling);

        function renderSizes() {
            sizesList.innerHTML = '';

            sizes.forEach(function(size, index) {
                const sizeRow = document.createElement('div');
                sizeRow.className = 'ppl-size-row';
                sizeRow.style.display = 'flex';
                sizeRow.style.gap = '10px';
                sizeRow.style.marginBottom = '10px';
                sizeRow.style.alignItems = 'center';
                sizeRow.setAttribute('data-index', index);

                // X Size
                const xInput = document.createElement('input');
                xInput.type = 'number';
                xInput.className = 'form-control';
                xInput.placeholder = 'Délka (cm)';
                xInput.value = size.xSize || '';
                xInput.style.width = '120px';
                xInput.addEventListener('input', function() {
                    const currentIndex = parseInt(sizeRow.getAttribute('data-index'));
                    if (sizes[currentIndex]) {
                        sizes[currentIndex].xSize = parseFloat(this.value) || 0;
                        updateHiddenInput();
                    }
                });

                // Y Size
                const yInput = document.createElement('input');
                yInput.type = 'number';
                yInput.className = 'form-control';
                yInput.placeholder = 'Šířka (cm)';
                yInput.value = size.ySize || '';
                yInput.style.width = '120px';
                yInput.addEventListener('input', function() {
                    const currentIndex = parseInt(sizeRow.getAttribute('data-index'));
                    if (sizes[currentIndex]) {
                        sizes[currentIndex].ySize = parseFloat(this.value) || 0;
                        updateHiddenInput();
                    }
                });

                // Z Size
                const zInput = document.createElement('input');
                zInput.type = 'number';
                zInput.className = 'form-control';
                zInput.placeholder = 'Výška (cm)';
                zInput.value = size.zSize || '';
                zInput.style.width = '120px';
                zInput.addEventListener('input', function() {
                    const currentIndex = parseInt(sizeRow.getAttribute('data-index'));
                    if (sizes[currentIndex]) {
                        sizes[currentIndex].zSize = parseFloat(this.value) || 0;
                        updateHiddenInput();
                    }
                });

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm';
                removeBtn.innerHTML = '&times;';
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentIndex = parseInt(sizeRow.getAttribute('data-index'));
                    sizes.splice(currentIndex, 1);
                    updateHiddenInput();
                    renderSizes();
                });

                sizeRow.appendChild(xInput);
                sizeRow.appendChild(yInput);
                sizeRow.appendChild(zInput);
                sizeRow.appendChild(removeBtn);
                sizesList.appendChild(sizeRow);
            });
        }

        function updateHiddenInput() {
            hiddenInput.value = JSON.stringify(sizes);
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            sizes.push({
                xSize: 0,
                ySize: 0,
                zSize: 0
            });
            renderSizes();
            updateHiddenInput();
        });

        // Initial render
        renderSizes();
    });
})();
