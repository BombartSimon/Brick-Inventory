import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["input", "container"]
    static values = { url: String }

    connect() {
        console.log("Search controller connected")
        this.originalContent = this.containerTarget.innerHTML
    }

    search() {
        const query = this.inputTarget.value.trim()

        clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
            this.performSearch(query)
        }, 400)
    }

    performSearch(query) {
        const url = new URL(this.urlValue, window.location.origin)

        if (query.length > 0) {
            url.searchParams.set('search', query)
        } else {
            url.searchParams.delete('search')
        }

        // Faire la requête AJAX
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                // Extraire le contenu du container depuis la réponse
                const parser = new DOMParser()
                const doc = parser.parseFromString(html, 'text/html')
                const newContainer = doc.querySelector('[data-search-target="container"]')

                if (newContainer) {
                    // Remplacer le contenu du container
                    this.containerTarget.innerHTML = newContainer.innerHTML

                    // Mettre à jour l'URL dans la barre d'adresse sans recharger
                    window.history.pushState({}, '', url.toString())
                }
            })
            .catch(error => {
                console.error('Error when searching:', error)
                this.containerTarget.innerHTML = this.originalContent
            })
    }
}
