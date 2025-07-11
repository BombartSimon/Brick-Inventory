import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [
        "partSelector",
        "searchInput",
        "selectWrapper",
        "partSelect",
        "partPreview",
        "previewName",
        "previewPartNum",
        "previewQuantity",
        "quantityInput",
        "quantityFeedback",
        "feedbackMessage",
        "submitBtn"
    ]

    connect() {
        this.originalOptions = this.getSelectOptions()
        this.updateSubmitButton()
    }

    getSelectOptions() {
        const options = []
        for (let option of this.partSelectTarget.options) {
            if (option.value) {
                options.push({
                    value: option.value,
                    text: option.textContent,
                    element: option
                })
            }
        }
        return options
    }

    filterParts(event) {
        const searchTerm = event.target.value.toLowerCase()

        // Clear current options except the placeholder
        while (this.partSelectTarget.options.length > 1) {
            this.partSelectTarget.removeChild(this.partSelectTarget.lastChild)
        }

        // Filter and add matching options
        const filteredOptions = this.originalOptions.filter(option =>
            option.text.toLowerCase().includes(searchTerm)
        )

        filteredOptions.forEach(option => {
            const newOption = option.element.cloneNode(true)
            this.partSelectTarget.appendChild(newOption)
        })

        // If no results, show a message
        if (filteredOptions.length === 0 && searchTerm) {
            const noResultOption = document.createElement('option')
            noResultOption.disabled = true
            noResultOption.textContent = `No parts found for "${searchTerm}"`
            this.partSelectTarget.appendChild(noResultOption)
        }

        // Reset selection if current value is no longer available
        if (this.partSelectTarget.value && !filteredOptions.find(opt => opt.value === this.partSelectTarget.value)) {
            this.partSelectTarget.value = ""
            this.hidePartPreview()
        }

        this.updateSubmitButton()
    }

    onPartChange(event) {
        const selectedOption = event.target.selectedOptions[0]

        if (selectedOption && selectedOption.value) {
            this.showPartPreview(selectedOption)
        } else {
            this.hidePartPreview()
        }

        this.validateQuantity()
        this.updateSubmitButton()
    }

    showPartPreview(option) {
        const text = option.textContent.trim()

        // Parse the format: "PART_NUM - PART_NAME (qty: X)"
        const matches = text.match(/^(.+?)\s*-\s*(.+?)\s*\(qty:\s*(\d+)\)$/)

        if (matches) {
            const [, partNum, partName, quantity] = matches

            this.previewNameTarget.textContent = partName.trim()
            this.previewPartNumTarget.textContent = `#${partNum.trim()}`
            this.previewQuantityTarget.textContent = `Qty: ${quantity}`

            // Store max quantity for validation
            this.maxQuantity = parseInt(quantity)

            this.partPreviewTarget.style.display = 'block'
        }
    }

    hidePartPreview() {
        this.partPreviewTarget.style.display = 'none'
        this.maxQuantity = 999
    }

    increaseQuantity() {
        const currentValue = parseInt(this.quantityInputTarget.value) || 1
        const maxValue = this.maxQuantity || 999

        if (currentValue < maxValue) {
            this.quantityInputTarget.value = currentValue + 1
            this.validateQuantity()
            this.updateSubmitButton()
        }
    }

    decreaseQuantity() {
        const currentValue = parseInt(this.quantityInputTarget.value) || 1

        if (currentValue > 1) {
            this.quantityInputTarget.value = currentValue - 1
            this.validateQuantity()
            this.updateSubmitButton()
        }
    }

    validateQuantity() {
        const quantity = parseInt(this.quantityInputTarget.value) || 0
        const maxQuantity = this.maxQuantity || 999
        const hasPartSelected = this.partSelectTarget.value !== ""

        if (!hasPartSelected) {
            this.hideQuantityFeedback()
            return false
        }

        let isValid = true
        let message = ""
        let type = "success"

        if (quantity < 1) {
            isValid = false
            message = "Quantity must be at least 1"
            type = "error"
        } else if (quantity > maxQuantity) {
            isValid = false
            message = `Cannot exceed ${maxQuantity} (total available in set)`
            type = "error"
        } else if (quantity === maxQuantity) {
            isValid = true
            message = "All pieces of this part will be marked as missing"
            type = "warning"
        } else {
            isValid = true
            message = "Quantity looks good!"
            type = "success"
        }

        this.showQuantityFeedback(message, type)
        return isValid
    }

    showQuantityFeedback(message, type) {
        this.feedbackMessageTarget.textContent = message
        this.quantityFeedbackTarget.className = `quantity-feedback ${type}`
        this.quantityFeedbackTarget.style.display = 'block'
    }

    hideQuantityFeedback() {
        this.quantityFeedbackTarget.style.display = 'none'
    }

    updateSubmitButton() {
        const hasPartSelected = this.partSelectTarget.value !== ""
        const hasValidQuantity = this.validateQuantity()
        const quantity = parseInt(this.quantityInputTarget.value) || 0

        const isFormValid = hasPartSelected && hasValidQuantity && quantity >= 1

        this.submitBtnTarget.disabled = !isFormValid

        if (isFormValid) {
            this.submitBtnTarget.classList.remove('btn-secondary')
            this.submitBtnTarget.classList.add('btn-primary')
        } else {
            this.submitBtnTarget.classList.remove('btn-primary')
            this.submitBtnTarget.classList.add('btn-secondary')
        }
    }

    // Add some visual enhancements
    searchInputTargetConnected() {
        this.searchInputTarget.addEventListener('focus', () => {
            this.searchInputTarget.parentElement.style.transform = 'scale(1.02)'
        })

        this.searchInputTarget.addEventListener('blur', () => {
            this.searchInputTarget.parentElement.style.transform = 'scale(1)'
        })
    }

    partSelectTargetConnected() {
        this.partSelectTarget.addEventListener('focus', () => {
            this.selectWrapperTarget.style.transform = 'scale(1.02)'
        })

        this.partSelectTarget.addEventListener('blur', () => {
            this.selectWrapperTarget.style.transform = 'scale(1)'
        })
    }
}
