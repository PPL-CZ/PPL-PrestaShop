/**
 * PPL Size Category - Single package size for categories
 * Allows defining one package dimension for a category
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const hiddenInput = document.querySelector('.ppl-size-category-data');
        if (!hiddenInput) return;

        const label = hiddenInput.getAttribute('data-label') || 'Velikost balíku';
        const description = hiddenInput.getAttribute('data-description') || 'Určuje, s jakou velikostí pracovat pro zjištění, zda je možná doprava (rozměry v cm)';

        // Parse existing size
        let size = null;
        try {
            size = JSON.parse(hiddenInput.value);
        } catch (e) {
            size = null;
        }

        // Create UI container
        const container = document.createElement('div');
        container.className = 'ppl-size-category-container';
        container.style.marginTop = '10px';
        container.style.marginBottom = '15px';

        // Label
        const labelEl = document.createElement('label');
        labelEl.className = 'form-control-label';
        labelEl.textContent = label;
        container.appendChild(labelEl);

        // Description
        if (description) {
            const descEl = document.createElement('p');
            descEl.className = 'help-block';
            descEl.textContent = description;
            descEl.style.marginBottom = '10px';
            container.appendChild(descEl);
        }

        // Create size inputs
        const sizeRow = document.createElement('div');
        sizeRow.className = 'ppl-size-category-row';
        sizeRow.style.display = 'flex';
        sizeRow.style.gap = '10px';
        sizeRow.style.marginTop = '10px';
        sizeRow.style.alignItems = 'center';

        // X Size
        const xInput = document.createElement('input');
        xInput.type = 'number';
        xInput.className = 'form-control';
        xInput.placeholder = 'Délka (cm)';
        xInput.value = (size && size.xSize) || '';
        xInput.style.width = '120px';
        xInput.addEventListener('input', updateSize);

        // Y Size
        const yInput = document.createElement('input');
        yInput.type = 'number';
        yInput.className = 'form-control';
        yInput.placeholder = 'Šířka (cm)';
        yInput.value = (size && size.ySize) || '';
        yInput.style.width = '120px';
        yInput.addEventListener('input', updateSize);

        // Z Size
        const zInput = document.createElement('input');
        zInput.type = 'number';
        zInput.className = 'form-control';
        zInput.placeholder = 'Výška (cm)';
        zInput.value = (size && size.zSize) || '';
        zInput.style.width = '120px';
        zInput.addEventListener('input', updateSize);

        // Clear button
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'btn btn-secondary btn-sm';
        clearBtn.textContent = 'Vymazat';
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            xInput.value = '';
            yInput.value = '';
            zInput.value = '';
            updateSize();
        });

        function updateSize() {
            const x = parseFloat(xInput.value) || 0;
            const y = parseFloat(yInput.value) || 0;
            const z = parseFloat(zInput.value) || 0;

            if (x > 0 || y > 0 || z > 0) {
                size = {
                    xSize: x,
                    ySize: y,
                    zSize: z
                };
                hiddenInput.value = JSON.stringify(size);
            } else {
                size = null;
                hiddenInput.value = 'null';
            }
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        sizeRow.appendChild(xInput);
        sizeRow.appendChild(yInput);
        sizeRow.appendChild(zInput);
        sizeRow.appendChild(clearBtn);
        container.appendChild(sizeRow);

        // Insert UI after hidden input
        hiddenInput.parentNode.insertBefore(container, hiddenInput.nextSibling);
    });
})();
