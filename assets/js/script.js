// Global variables
let currentUser = null
let wishlistItems = []
const products = []
const filteredProducts = []
const userWishlist = new Set()

// Enhanced blocking system - harus dijalankan sebelum DOM loaded
;(() => {
  // Override window.alert dengan blocking yang lebih kuat
  const originalAlert = window.alert
  window.alert = (message) => {
    // Daftar kata kunci yang akan diblokir
    const blockedKeywords = [
      "localhost",
      "silakan login",
      "terjadi kesalahan saat",
      "email sudah terdaftar",
      "ulasan",
      "field harus diisi",
      "semua field harus diisi",
      "harus diisi",
      "wajib diisi",
      "tidak boleh kosong",
      "required",
      "validation",
    ]

    const shouldBlock = blockedKeywords.some((keyword) => message.toLowerCase().includes(keyword.toLowerCase()))

    if (shouldBlock) {
      console.log("Blocked unwanted alert:", message)
      return // Blokir alert ini
    }

    // Izinkan alert lainnya
    originalAlert(message)
  }

  // Override semua fungsi notifikasi yang mungkin ada
  const blockNotificationFunctions = [
    "showNotification",
    "showToastNotification",
    "showUnifiedNotification",
    "displayNotification",
    "notify",
    "toast",
  ]

  blockNotificationFunctions.forEach((funcName) => {
    if (window[funcName]) {
      const originalFunc = window[funcName]
      window[funcName] = (message, type) => {
        const blockedKeywords = [
          "field harus diisi",
          "semua field harus diisi",
          "harus diisi",
          "wajib diisi",
          "tidak boleh kosong",
          "berhasil regis silahkan login",
          "terjadi kesalahan saat",
          "terjadi kesalahan saat",
          "email sudah terdaftar",
        ]

        const shouldBlock = blockedKeywords.some((keyword) => message.toLowerCase().includes(keyword.toLowerCase()))

        if (shouldBlock) {
          console.log(`Blocked unwanted ${funcName}:`, message)
          return
        }

        originalFunc.call(this, message, type)
      }
    }
  })

  // Override mabarApp showNotification jika ada
  let originalMabarApp = window.mabarApp
  Object.defineProperty(window, "mabarApp", {
    get: () => originalMabarApp,
    set: (value) => {
      if (value && value.showNotification) {
        const originalShowNotification = value.showNotification
        value.showNotification = function (message, type) {
          const blockedKeywords = [
            "field harus diisi",
            "semua field harus diisi",
            "harus diisi",
            "wajib diisi",
            "tidak boleh kosong",
          ]

          const shouldBlock = blockedKeywords.some((keyword) => message.toLowerCase().includes(keyword.toLowerCase()))

          if (shouldBlock) {
            console.log("Blocked mabarApp notification:", message)
            return
          }

          originalShowNotification.call(this, message, type)
        }
      }
      originalMabarApp = value
    },
  })

  // Blokir elemen DOM yang muncul dengan pesan tertentu
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1 && node.textContent) {
          const text = node.textContent.toLowerCase()

          const blockedTexts = [
            "field harus diisi",
            "semua field harus diisi",
            "harus diisi",
            "wajib diisi",
            "tidak boleh kosong",
            "berhasil regis silahkan login",
            "terjadi kesalahan saat",
            "email sudah terdaftar",
          ]

          const shouldHide = blockedTexts.some((blockedText) => text.includes(blockedText))

          if (shouldHide) {
            node.style.display = "none"
            console.log("Hidden unwanted notification element:", text)
            // Hapus elemen setelah delay singkat
            setTimeout(() => {
              if (node.parentNode) {
                node.parentNode.removeChild(node)
              }
            }, 100)
          }
        }
      })
    })
  })

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  })

  // Blokir event listener yang mungkin menampilkan notifikasi
  const originalAddEventListener = EventTarget.prototype.addEventListener
  EventTarget.prototype.addEventListener = function (type, listener, options) {
    if (typeof listener === "function") {
      const wrappedListener = function (event) {
        try {
          return listener.call(this, event)
        } catch (error) {
          // Jika error mengandung kata kunci yang diblokir, jangan tampilkan
          const errorMessage = error.message || error.toString()
          const blockedKeywords = ["field harus diisi", "semua field harus diisi", "harus diisi", "validation"]

          const shouldBlock = blockedKeywords.some((keyword) =>
            errorMessage.toLowerCase().includes(keyword.toLowerCase()),
          )

          if (!shouldBlock) {
            throw error
          }
          console.log("Blocked error notification:", errorMessage)
        }
      }
      return originalAddEventListener.call(this, type, wrappedListener, options)
    }
    return originalAddEventListener.call(this, type, listener, options)
  }
})()

// DOM Content Loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeApp()
  window.mabarApp = new MabarApp()
})

// Modern JavaScript with ES6+ features
class MabarApp {
  constructor() {
    this.currentUser = null
    this.products = []
    this.filteredProducts = []
    this.wishlist = new Set()
    this.theme = localStorage.getItem("theme") || "light"
    this.currentCategory = null
    this.currentFilters = {}
    this.currentSort = "popularity"
    this.searchTerm = ""

    this.init()
  }

  async init() {
    this.setupTheme()
    this.setupEventListeners()
    this.setupIntersectionObserver()
    this.setActiveNavigation()

    // Load user wishlist if logged in
    await this.loadUserWishlist()

    // Load products based on current page
    const currentPage = window.location.pathname
    console.log("Current page:", currentPage)

    if (currentPage.includes("index.php") || currentPage === "/" || currentPage.endsWith("/")) {
      await this.loadFeaturedProducts()
    } else if (currentPage.includes("joran.php")) {
      this.currentCategory = "joran"
      await this.loadProducts("joran")
    } else if (currentPage.includes("reel.php")) {
      this.currentCategory = "reel"
      await this.loadProducts("reel")
    } else if (currentPage.includes("umpan.php")) {
      this.currentCategory = "umpan"
      await this.loadProducts("umpan")
    } else if (currentPage.includes("aksesoris.php")) {
      this.currentCategory = "aksesoris"
      await this.loadProducts("aksesoris")
    }

    this.setupSmoothScrolling()
    this.restoreSearchTerm()
    this.setupProductDetailZoom()

    const wishlistGrid = document.getElementById("wishlistGrid")
    if (wishlistGrid) {
      const dataScript = document.getElementById("wishlist-data")
      if (dataScript) {
        try {
          const wishlistData = JSON.parse(dataScript.textContent)
          this.renderProducts(wishlistData, wishlistGrid)
        } catch (e) {
          console.error("Gagal mem-parsing data wishlist:", e)
        }
      }
    }
    const searchResultsGrid = document.getElementById("searchResultsGrid")
    if (searchResultsGrid) {
      const dataScript = document.getElementById("page-data")
      if (dataScript) {
        try {
          const pageData = JSON.parse(dataScript.textContent)
          if (pageData.products) {
            this.renderProducts(pageData.products, searchResultsGrid)
          }
        } catch (e) {
          console.error("Gagal mem-parsing data hasil pencarian:", e)
        }
      }
    }
  }

  // Set active navigation based on current page
  setActiveNavigation() {
    const currentPage = window.location.pathname
    const navLinks = document.querySelectorAll(".nav-link")
    const bodyCategory = document.body.dataset.category

    navLinks.forEach((link) => {
      link.classList.remove("active")
      const href = link.getAttribute("href").replace(".php", "")

      // Logika untuk halaman biasa (joran.php, reel.php, dll.)
      if (currentPage.includes(href + ".php")) {
        link.classList.add("active")
      }
      // Logika khusus untuk halaman product-detail.php
      else if (currentPage.includes("product-detail.php") && bodyCategory === href) {
        link.classList.add("active")
      }
      // Logika untuk halaman Beranda
      else if ((currentPage.endsWith("/") || currentPage.endsWith("index.php")) && href === "index") {
        link.classList.add("active")
      }
    })
  }

  // Restore search term from URL or localStorage
  restoreSearchTerm() {
    const urlParams = new URLSearchParams(window.location.search)
    const searchQuery = urlParams.get("q")

    if (searchQuery) {
      this.searchTerm = searchQuery
      const searchInput = document.getElementById("searchInput")
      if (searchInput) {
        searchInput.value = searchQuery
      }
    }
  }

  async loadUserWishlist() {
    try {
      const response = await fetch("api/wishlist.php")
      const result = await response.json()
      if (result.success && result.wishlist) {
        this.wishlist = new Set(result.wishlist.map((id) => id.toString()))
      }
    } catch (error) {
      console.log("Could not load wishlist:", error)
    }
  }

  setupTheme() {
    document.documentElement.setAttribute("data-theme", this.theme)
    const themeToggle = document.getElementById("themeToggle")
    if (themeToggle) {
      const icon = themeToggle.querySelector("i")
      if (icon) {
        icon.className = this.theme === "dark" ? "fas fa-sun" : "fas fa-moon"
      }
    }
  }

  setupEventListeners() {
    // Theme toggle
    const themeToggle = document.getElementById("themeToggle")
    if (themeToggle) {
      themeToggle.addEventListener("click", () => this.toggleTheme())
    }

    // Search functionality
    const searchInput = document.getElementById("searchInput")
    if (searchInput) {
      searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          e.preventDefault()
          // Langsung panggil performSearch dengan nilai dari input
          this.performSearch(e.target.value)
        }
      })
    }

    // Product search in category pages
    const productSearch = document.getElementById("productSearch")
    if (productSearch) {
      productSearch.addEventListener(
        "input",
        this.debounce((e) => {
          this.handleProductSearch(e.target.value)
        }, 300),
      )
    }

    // Sort select
    const sortSelect = document.getElementById("sortSelect")
    if (sortSelect) {
      sortSelect.addEventListener("change", (e) => {
        this.currentSort = e.target.value
        this.applyFilters()
      })
    }

    // Price range filter
    const priceRange = document.getElementById("priceRange")
    if (priceRange) {
      priceRange.addEventListener("change", (e) => {
        this.currentFilters.price_range = e.target.value
        this.applyFilters()
      })
    }

    // Category filters (checkboxes)
    const categoryCheckboxes = document.querySelectorAll('.filter-checkbox input[type="checkbox"]')
    categoryCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        this.updateCategoryFilters()
        this.applyFilters()
      })
    })

    // Modal functionality
    this.setupModalEvents()

    // Form submissions
    this.setupFormEvents()

    // Scroll events
    window.addEventListener(
      "scroll",
      this.throttle(() => {
        this.handleScroll()
      }, 16),
    )

    // Product interactions
    this.setupProductEvents()
  }

  performSearch(query) {
    if (query.trim()) {
      const dropdown = document.getElementById("searchResultsDropdown")
      if (dropdown) dropdown.style.display = "none"
      window.location.href = `search.php?q=${encodeURIComponent(query.trim())}`
    }
  }

  updateCategoryFilters() {
    const checkedBoxes = document.querySelectorAll('.filter-checkbox input[type="checkbox"]:checked')
    const subcategories = Array.from(checkedBoxes).map((cb) => cb.value)

    if (subcategories.length > 0) {
      this.currentFilters.subcategory = subcategories
    } else {
      delete this.currentFilters.subcategory
    }
  }

  setupModalEvents() {
    const modal = document.getElementById("loginModal")
    if (modal) {
      // Close modal on outside click
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          this.closeLoginModal()
        }
      })

      // Close modal on escape key
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && modal.style.display === "block") {
          this.closeLoginModal()
        }
      })
    }
  }

  setupFormEvents() {
    const loginForm = document.getElementById("loginForm")
    const registerForm = document.getElementById("registerForm")

    if (loginForm) {
      loginForm.addEventListener("submit", (e) => this.handleLogin(e))
    }

    if (registerForm) {
      registerForm.addEventListener("submit", (e) => this.handleRegister(e))
    }
  }

  setupProductEvents() {
    // Delegate event handling for dynamic content
    document.addEventListener("click", (e) => {
      // Cek apakah user adalah admin
      const isAdmin =
        document.querySelector(".user-menu") !== null &&
        document.querySelector(".user-menu").classList.contains("admin-menu")

      if (e.target.closest(".wishlist-btn") && !isAdmin) {
        e.preventDefault()
        const productId = e.target.closest(".wishlist-btn").dataset.productId
        this.toggleWishlist(productId)
      }

      if (e.target.closest(".product-card")) {
        const productSlug = e.target.closest(".product-card").dataset.slug
        if (productSlug && !e.target.closest(".wishlist-btn") && !e.target.closest(".btn")) {
          window.location.href = `product-detail.php?slug=${productSlug}`
        }
      }
    })
  }

  setupIntersectionObserver() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in")
        }
      })
    }, observerOptions)

    // Observe elements for animation
    const elementsToObserve = document.querySelectorAll(".category-card, .product-card, .section-header")
    elementsToObserve.forEach((el) => observer.observe(el))
  }

  setupSmoothScrolling() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", (e) => {
        e.preventDefault()
        const target = document.querySelector(anchor.getAttribute("href"))
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
        }
      })
    })
  }

  setupProductDetailZoom() {
    const mainImage = document.querySelector(".main-image img")
    if (!mainImage) return

    const container = mainImage.parentElement
    let lens,
      result,
      isZoomActive = false

    // Tunggu gambar selesai dimuat
    if (mainImage.complete) {
      initializeZoom()
    } else {
      mainImage.addEventListener("load", initializeZoom)
    }

    function initializeZoom() {
      container.addEventListener("mouseenter", showZoom)
      container.addEventListener("mouseleave", hideZoom)
      container.addEventListener("mousemove", moveLens)
      mainImage.addEventListener("dragstart", (e) => e.preventDefault())
    }

    function showZoom() {
      if (isZoomActive) return
      isZoomActive = true

      // Buat lens jika belum ada
      if (!lens) {
        lens = document.createElement("div")
        lens.className = "zoom-lens"
        container.appendChild(lens)
      }

      if (!result) {
        result = document.createElement("div")
        result.className = "zoom-result"
        document.body.appendChild(result)
      }

      const lensSize = 300
      const resultSize = 600

      lens.style.width = lensSize + "px"
      lens.style.height = lensSize + "px"
      result.style.width = resultSize + "px"
      result.style.height = resultSize + "px"

      // Hitung rasio zoom
      const zoomRatio = resultSize / lensSize

      // Set background image untuk zoom result
      result.style.backgroundImage = `url('${mainImage.src}')`
      result.style.backgroundSize = `${mainImage.naturalWidth * zoomRatio}px ${mainImage.naturalHeight * zoomRatio}px`

      // Posisikan result container
      const containerRect = container.getBoundingClientRect()
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop
      const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft

      let resultLeft = containerRect.right + scrollLeft + 20
      let resultTop = containerRect.top + scrollTop

      // Cek apakah result keluar dari layar
      if (resultLeft + resultSize > window.innerWidth + scrollLeft) {
        resultLeft = containerRect.left + scrollLeft - resultSize - 20
      }

      // Cek apakah result keluar dari layar vertikal
      if (resultTop + resultSize > window.innerHeight + scrollTop) {
        resultTop = window.innerHeight + scrollTop - resultSize - 20
      }

      result.style.left = resultLeft + "px"
      result.style.top = resultTop + "px"
      result.style.position = "absolute"

      // Tampilkan elemen
      lens.style.display = "block"
      result.style.display = "block"
      lens.style.opacity = "1"
      result.style.opacity = "1"

      container.style.cursor = "crosshair"
    }

    function moveLens(e) {
      if (!lens || !result || !isZoomActive) return

      e.preventDefault()
      const pos = getCursorPos(e)

      // Hitung posisi lens
      let x = pos.x - lens.offsetWidth / 2
      let y = pos.y - lens.offsetHeight / 2

      // Batasi lens dalam area container
      const maxX = container.offsetWidth - lens.offsetWidth
      const maxY = container.offsetHeight - lens.offsetHeight

      x = Math.max(0, Math.min(x, maxX))
      y = Math.max(0, Math.min(y, maxY))

      // Set posisi lens
      lens.style.left = x + "px"
      lens.style.top = y + "px"

      // Hitung rasio antara ukuran asli gambar dan ukuran yang ditampilkan
      const scaleX = mainImage.naturalWidth / mainImage.offsetWidth
      const scaleY = mainImage.naturalHeight / mainImage.offsetHeight

      // Hitung rasio zoom dari hasil bagi ukuran result dengan lensa
      const zoomRatioX = result.offsetWidth / lens.offsetWidth
      const zoomRatioY = result.offsetHeight / lens.offsetHeight

      // Terapkan semua rasio pada posisi background
      const bgX = x * scaleX * zoomRatioX
      const bgY = y * scaleY * zoomRatioY

      result.style.backgroundPosition = `-${bgX}px -${bgY}px`
    }

    function getCursorPos(e) {
      const containerRect = container.getBoundingClientRect()
      return {
        x: e.clientX - containerRect.left,
        y: e.clientY - containerRect.top,
      }
    }

    function hideZoom() {
      if (!isZoomActive) return
      isZoomActive = false

      if (lens) {
        lens.style.opacity = "0"
        setTimeout(() => {
          lens.style.display = "none"
        }, 200)
      }

      if (result) {
        result.style.opacity = "0"
        setTimeout(() => {
          result.style.display = "none"
        }, 200)
      }

      container.style.cursor = "default"
    }

    // Handle window events
    window.addEventListener("resize", hideZoom)
    window.addEventListener("scroll", hideZoom)
  }

  toggleTheme() {
    this.theme = this.theme === "light" ? "dark" : "light"
    document.documentElement.setAttribute("data-theme", this.theme)
    localStorage.setItem("theme", this.theme)

    const themeToggle = document.getElementById("themeToggle")
    if (themeToggle) {
      const icon = themeToggle.querySelector("i")
      if (icon) {
        icon.className = this.theme === "dark" ? "fas fa-sun" : "fas fa-moon"
      }
    }

    // Add transition effect
    document.body.style.transition = "background-color 0.3s ease, color 0.3s ease"
    setTimeout(() => {
      document.body.style.transition = ""
    }, 300)
    this.handleScroll()
  }

  handleScroll() {
    const header = document.getElementById("header")
    if (header) {
      if (window.scrollY > 100) {
        header.style.background = this.theme === "dark" ? "rgba(15, 23, 42, 0.95)" : "rgba(255, 255, 255, 0.95)"
        header.style.boxShadow = "0 4px 20px rgba(0, 0, 0, 0.1)"
      } else {
        header.style.background = this.theme === "dark" ? "rgba(15, 23, 42, 0.8)" : "rgba(255, 255, 255, 0.8)"
        header.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)"
      }
    }
  }

  // GANTI metode performSearch
  performSearch(query) {
    if (query.trim()) {
      const dropdown = document.getElementById("searchResultsDropdown")
      if (dropdown) dropdown.style.display = "none"
      window.location.href = `search.php?q=${encodeURIComponent(query.trim())}`
    }
  }

  // TAMBAHKAN DUA METODE BARU INI di dalam kelas
  highlightSearchTerm(text, term) {
    if (!term) return text
    const regex = new RegExp(`(${term})`, "gi")
    return text.replace(regex, "<strong>$1</strong>")
  }

  setupDropdownClickOutside() {
    const handleClickOutside = (e) => {
      const searchContainer = document.querySelector(".nav-search")
      if (searchContainer && !searchContainer.contains(e.target)) {
        const dropdown = document.getElementById("searchResultsDropdown")
        if (dropdown) dropdown.style.display = "none"
        searchContainer.classList.remove("dropdown-active")
        document.removeEventListener("click", handleClickOutside)
      }
    }
    document.addEventListener("click", handleClickOutside)
  }

  // Enhanced Search Input Setup
  setupSearchInput() {
    const searchInput = document.getElementById("searchInput")
    if (!searchInput) return

    // Input event with debounce
    searchInput.addEventListener(
      "input",
      this.debounce((e) => {
        this.searchTerm = e.target.value
        this.handleSearch(e.target.value)
      }, 300),
    )

    // Enter key to go to search results
    searchInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault()
        this.performSearch(this.searchTerm)
      }
    })

    // Focus event
    searchInput.addEventListener("focus", (e) => {
      if (e.target.value.trim().length >= 2) {
        this.handleSearch(e.target.value)
      }
    })

    // Escape key to close dropdown
    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const dropdown = document.getElementById("searchResultsDropdown")
        const searchContainer = document.querySelector(".nav-search")

        if (dropdown) dropdown.style.display = "none"
        searchContainer?.classList.remove("dropdown-active")
      }
    })
  }

  async handleProductSearch(query) {
    this.currentFilters.search = query
    await this.applyFilters()
  }

  showSearchDropdown(results) {
    const searchContainer = document.querySelector(".nav-search")
    if (!searchContainer) return

    let dropdown = searchContainer.querySelector(".search-dropdown")

    if (!dropdown) {
      dropdown = document.createElement("div")
      dropdown.className = "search-dropdown"
      searchContainer.appendChild(dropdown)
    }

    if (results.length === 0) {
      dropdown.innerHTML = '<div class="search-no-results">Tidak ada hasil ditemukan</div>'
    } else {
      dropdown.innerHTML = results
        .slice(0, 5)
        .map(
          (product) => `
          <div class="search-result-item" onclick="location.href='product-detail.php?slug=${product.slug}'">
            <img src="${product.image}" alt="${product.name}" class="search-result-image">
            <div class="search-result-info">
              <div class="search-result-name">${product.name}</div>
              <div class="search-result-price">${this.formatPrice(product.price)}</div>
            </div>
          </div>
        `,
        )
        .join("")
    }

    dropdown.style.display = "block"

    // Hide dropdown when clicking outside
    setTimeout(() => {
      document.addEventListener(
        "click",
        (e) => {
          if (!searchContainer.contains(e.target)) {
            dropdown.style.display = "none"
          }
        },
        { once: true },
      )
    }, 100)
  }

  async loadFeaturedProducts() {
    const container = document.getElementById("featuredProducts")
    if (!container) return

    try {
      this.showLoading(container)
      console.log("Loading featured products...")

      // Try using the products API first
      const response = await fetch("api/products.php?sort=popularity&limit=6")
      const result = await response.json()

      console.log("Featured products response:", result)

      if (result.success && result.products && result.products.length > 0) {
        this.renderProducts(result.products, container)
      } else {
        // Fallback to search API
        const fallbackResponse = await fetch("api/search.php?sort=popularity&limit=6")
        const fallbackProducts = await fallbackResponse.json()

        console.log("Fallback products:", fallbackProducts)

        if (fallbackProducts && Array.isArray(fallbackProducts) && fallbackProducts.length > 0) {
          this.renderProducts(fallbackProducts, container)
        } else {
          this.showError(container, "Tidak ada produk ditemukan")
        }
      }
    } catch (error) {
      console.error("Error loading featured products:", error)
      this.showError(container, "Gagal memuat produk unggulan")
    }
  }

  async loadProducts(category = null) {
    const container = document.getElementById("productsGrid")
    if (!container) return

    try {
      this.showLoading(container)
      console.log("Loading products for category:", category)

      const params = new URLSearchParams()
      if (category) params.append("category", category)
      params.append("sort", this.currentSort)

      // Add filters
      Object.entries(this.currentFilters).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          params.append(key, value.join(","))
        } else if (value) {
          params.append(key, value)
        }
      })

      const response = await fetch(`api/products.php?${params.toString()}`)
      const result = await response.json()

      console.log("Products response:", result)

      if (result.success && result.products && Array.isArray(result.products)) {
        this.products = result.products
        this.filteredProducts = [...result.products]
        this.renderProducts(result.products, container)
        this.updateProductCount()
      } else {
        // Fallback to search API
        const fallbackResponse = await fetch(`api/search.php?${params.toString()}`)
        const fallbackProducts = await fallbackResponse.json()

        console.log("Fallback products:", fallbackProducts)

        if (fallbackProducts && Array.isArray(fallbackProducts)) {
          this.products = fallbackProducts
          this.filteredProducts = [...fallbackProducts]
          this.renderProducts(fallbackProducts, container)
          this.updateProductCount()
        } else {
          this.showError(container, "Gagal memuat produk")
        }
      }
    } catch (error) {
      console.error("Error loading products:", error)
      this.showError(container, "Gagal memuat produk")
    }
  }

  async applyFilters() {
    await this.loadProducts(this.currentCategory)
  }

  renderProducts(products, container) {
    if (products.length === 0) {
      container.innerHTML = `
        <div class="no-products">
          <i class="fas fa-fish"></i>
          <h3>Tidak ada produk ditemukan</h3>
          <p>Coba ubah filter atau kata kunci pencarian Anda</p>
        </div>
      `
      return
    }

    container.innerHTML = products.map((product) => this.createProductCard(product)).join("")

    // Setup intersection observer for new cards
    const newCards = container.querySelectorAll(".product-card")
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in")
        }
      })
    }, observerOptions)

    newCards.forEach((card) => observer.observe(card))
  }

  createProductCard(product) {
    const isInWishlist = this.wishlist.has(product.id.toString())
    const isAdmin =
      document.querySelector(".user-menu") !== null &&
      document.querySelector(".user-menu").classList.contains("admin-menu")
    const isLoggedIn = this.isLoggedIn()

    return `
      <div class="product-card" data-slug="${product.slug}">
        <div class="product-image">
          <img src="${product.image}" alt="${product.name}" loading="lazy">
          ${
            !isAdmin
              ? `
            <button class="wishlist-btn ${isInWishlist ? "active" : ""}" 
                    data-product-id="${product.id}"
                    aria-label="Tambah ke wishlist">
              <i class="${isInWishlist ? "fas" : "far"} fa-heart"></i>
            </button>
          `
              : ""
          }
          ${product.is_promo ? '<span class="product-badge promo">PROMO</span>' : ""}
          ${product.is_new ? '<span class="product-badge new">BARU</span>' : ""}
        </div>
        <div class="product-info">
          <div class="product-category">${product.subcategory || product.category_name}</div>
          <h3 class="product-name">${product.name}</h3>
          <div class="product-rating">
            ${this.generateStars(product.rating || 0)}
            <span class="rating-text">(${product.review_count || 0})</span>
          </div>
          <div class="product-specs">
            ${this.renderProductSpecs(product)}
          </div>
          <div class="product-price">
            ${this.formatPrice(product.price)}
          </div>
          <div class="product-actions">
            <a href="product-detail.php?slug=${product.slug}" class="btn btn-primary">
              <i class="fas fa-eye"></i>
              Detail
            </a>
            ${
              isAdmin
                ? `
              <a href="edit-product.php?id=${product.id}" class="btn btn-secondary">
                <i class="fas fa-edit"></i>
                Edit
              </a>
            `
                : `
              ${
                product.shopee_link
                  ? `
                <a href="javascript:void(0)" 
                   onclick="${isLoggedIn ? `window.open('${product.shopee_link}', '_blank')` : `requireAuth(() => window.open('${product.shopee_link}', '_blank'), 'melihat produk di toko online')`}"
                   class="btn btn-secondary">
                  <i class="fas fa-shopping-cart"></i>
                  Beli
                </a>
              `
                  : ""
              }
            `
            }
          </div>
        </div>
      </div>
    `
  }

  generateStars(rating) {
    let stars = ""
    for (let i = 1; i <= 5; i++) {
      stars += `<i class="fas fa-star ${i <= rating ? "active" : ""}"></i>`
    }
    return stars
  }

  renderProductSpecs(product) {
    const specs = []

    // For joran - remove target_fish
    if (product.rod_length)
      specs.push(
        `<div class="spec-item"><span class="spec-label">Panjang:</span> <span class="spec-value">${product.rod_length}</span></div>`,
      )
    if (product.gear_ratio)
      specs.push(
        `<div class="spec-item"><span class="spec-label">Gear Ratio:</span> <span class="spec-value">${product.gear_ratio}</span></div>`,
      )
    if (product.weight)
      specs.push(
        `<div class="spec-item"><span class="spec-label">Berat:</span> <span class="spec-value">${product.weight}</span></div>`,
      )
    if (product.rod_action)
      specs.push(
        `<div class="spec-item"><span class="spec-label">Action:</span> <span class="spec-value">${product.rod_action}</span></div>`,
      )
    if (product.bearings)
      specs.push(
        `<div class="spec-item"><span class="spec-label">Bearings:</span> <span class="spec-value">${product.bearings}</span></div>`,
      )

    return specs.slice(0, 3).join("")
  }

  async toggleWishlist(productId) {
    // Cek apakah user adalah admin
    const isAdmin =
      document.querySelector(".user-menu") !== null &&
      document.querySelector(".user-menu").classList.contains("admin-menu")

    if (isAdmin) {
      this.showToastNotification("Admin tidak memerlukan fitur wishlist", "info")
      return
    }

    if (!this.isLoggedIn()) {
      this.showToastNotification("Anda harus login untuk menambahkan produk ke daftar yang disukai", "info")
      setTimeout(() => {
        this.openAuthModal("login")
      }, 1500)
      return
    }

    try {
      const formData = new FormData()
      formData.append("product_id", productId)

      const response = await fetch("api/wishlist.php", {
        method: "POST",
        body: formData,
      })

      const result = await response.json()

      if (result.success) {
        if (result.action === "added") {
          this.wishlist.add(productId)
          this.showToastNotification("Produk berhasil ditambahkan ke daftar yang disukai!", "success")
        } else {
          this.wishlist.delete(productId)
          this.showToastNotification("Produk dihapus dari daftar yang disukai", "success")
        }

        this.updateWishlistButtons(productId, result.action)
      } else {
        this.showToastNotification(result.message, "error")
      }
    } catch (error) {
      console.error("Wishlist error:", error)
      this.showToastNotification("Terjadi kesalahan", "error")
    }
  }

  updateWishlistButtons(productId, action) {
    const buttons = document.querySelectorAll(`[data-product-id="${productId}"]`)
    buttons.forEach((button) => {
      const icon = button.querySelector("i")
      if (action === "added") {
        button.classList.add("active")
        icon.className = "fas fa-heart"
      } else {
        button.classList.remove("active")
        icon.className = "far fa-heart"
      }
    })
  }

  async handleLogin(e) {
    e.preventDefault()
    const form = e.target
    const formData = new FormData(form)
    const submitBtn = form.querySelector(".form-submit")

    try {
      this.setButtonLoading(submitBtn, true)
      const response = await fetch("auth/login.php", { method: "POST", body: formData })
      const result = await response.json()

      if (result.success) {
        this.closeLoginModal()
        this.showCustomPopup("✅ Berhasil Login!", result.message, "success")
        setTimeout(() => window.location.reload(), 2000)
      } else {
        this.showCustomPopup("❌ Gagal Login!", result.message, "error")
      }
    } catch (error) {
      this.showCustomPopup("❌ Gagal!", "Terjadi kesalahan koneksi.", "error")
    } finally {
      this.setButtonLoading(submitBtn, false)
    }
  }

  // GANTI metode handleRegister
  async handleRegister(e) {
    e.preventDefault()
    const form = e.target
    const formData = new FormData(form)
    const submitBtn = form.querySelector(".form-submit")

    if (formData.get("password") !== formData.get("confirm_password")) {
      this.showCustomPopup("❌ Gagal", "Konfirmasi password tidak cocok.", "error")
      return
    }

    try {
      this.setButtonLoading(submitBtn, true)
      const response = await fetch("auth/register.php", { method: "POST", body: formData })
      const result = await response.json()

      if (result.success) {
        this.closeLoginModal()
        this.showCustomPopup("🎉 Registrasi Berhasil!", result.message, "success")
        setTimeout(() => window.location.reload(), 2000)
      } else {
        this.showCustomPopup("❌ Gagal Register!", result.message, "error")
      }
    } catch (error) {
      this.showCustomPopup("❌ Gagal!", "Terjadi kesalahan koneksi.", "error")
    } finally {
      this.setButtonLoading(submitBtn, false)
    }
  }

  handleLogout(e) {
    if (e) e.preventDefault()

    // Tampilkan pop-up konfirmasi
    this.showConfirmationPopup("Konfirmasi Logout", "Apakah Anda yakin ingin keluar?", () => {
      // Aksi jika pengguna klik "Ya"
      fetch("auth/logout.php")
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            this.showCustomPopup("👋 Logout Berhasil!", "Terima kasih telah berkunjung.", "info")
            setTimeout(() => (window.location.href = "index.php"), 2000)
          }
        })
        .catch((err) => {
          console.error("Logout error:", err)
          window.location.href = "auth/logout.php" // Fallback
        })
    })
  }

  showConfirmationPopup(title, message, onConfirm) {
    // Hapus popup lama jika ada
    const existingPopup = document.getElementById("custom-popup-overlay")
    if (existingPopup) existingPopup.remove()

    const overlay = document.createElement("div")
    overlay.id = "custom-popup-overlay"
    overlay.className = "custom-popup-overlay"

    overlay.innerHTML = `
        <div class="custom-popup popup-confirm">
            <h3 class="popup-title">${title}</h3>
            <p class="popup-message">${message}</p>
            <div class="popup-actions">
                <button class="btn-confirm-no">Tidak</button>
                <button class="btn-confirm-yes">Ya</button>
            </div>
        </div>
    `

    document.body.appendChild(overlay)

    const closePopup = () => overlay.remove()

    overlay.querySelector(".btn-confirm-yes").addEventListener("click", () => {
      onConfirm() // Jalankan aksi jika "Ya"
      closePopup()
    })
    overlay.querySelector(".btn-confirm-no").addEventListener("click", closePopup)
  }

  showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll(".tab-content").forEach((tab) => {
      tab.classList.remove("active")
    })

    // Remove active class from all tab buttons
    document.querySelectorAll(".tab-btn").forEach((btn) => {
      btn.classList.remove("active")
    })

    // Show selected tab
    const targetTab = document.getElementById(tabName + "Tab")
    const targetBtn = event.target

    if (targetTab) targetTab.classList.add("active")
    if (targetBtn) targetBtn.classList.add("active")
  }

  joinWhatsAppCommunity() {
    if (!this.isLoggedIn()) {
      this.showToastNotification("Anda harus login untuk bergabung ke komunitas WhatsApp", "info")
      setTimeout(() => {
        this.openAuthModal("login")
      }, 1500)
      return
    }

    const whatsappUrl = "https://chat.whatsapp.com/ESRL1L9ImLlGvgBHAk85e6"
    window.open(whatsappUrl, "_blank")
  }

  scrollToRecommendations() {
    const target = document.getElementById("recommendations")
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  }

  scrollToCategories() {
    const target = document.getElementById("categories")
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  }

  // Utility methods
  formatPrice(price) {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(price)
  }

  isLoggedIn() {
    return document.querySelector(".user-menu") !== null
  }

  showLoading(container) {
    container.innerHTML = `
      <div class="loading">
        <div class="spinner"></div>
        <p>Memuat produk...</p>
      </div>
    `
  }

  showError(container, message) {
    container.innerHTML = `
      <div class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>Oops!</h3>
        <p>${message}</p>
        <button class="btn btn-primary" onclick="location.reload()">
          <i class="fas fa-refresh"></i>
          Coba Lagi
        </button>
      </div>
    `
  }

  setButtonLoading(button, loading) {
    if (loading) {
      button.disabled = true
      button.dataset.originalText = button.innerHTML
      button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...'
    } else {
      button.disabled = false
      button.innerHTML = button.dataset.originalText || "Submit"
    }
  }

  showToastNotification(message, type = "success") {
    // Panggil fungsi global yang sudah didefinisikan di header
    if (window.showToastNotification) {
      window.showToastNotification(message, type)
    }
  }

  updateProductCount() {
    const countElement = document.getElementById("productCount")
    if (countElement && this.products) {
      const total = this.products.length
      const showing = this.filteredProducts.length
      countElement.textContent = `Menampilkan ${showing} dari ${total} produk`
    }
  }

  // Performance utilities
  debounce(func, wait) {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  }

  throttle(func, limit) {
    let inThrottle
    return function () {
      const args = arguments

      if (!inThrottle) {
        func.apply(this, args)
        inThrottle = true
        setTimeout(() => (inThrottle = false), limit)
      }
    }
  }

  updateUIAfterLogin(user) {
    // Update header jika ada user menu
    const loginBtn = document.querySelector(".login-btn")
    const userMenu = document.querySelector(".user-menu")

    if (loginBtn && user) {
      // Hide login button
      loginBtn.style.display = "none"

      // Show user menu atau create user menu
      if (userMenu) {
        userMenu.style.display = "block"
        // Update user name di menu
        const userName = userMenu.querySelector(".user-name")
        if (userName) {
          userName.textContent = user.name
        }
      } else {
        // Create simple user indicator
        const userIndicator = document.createElement("div")
        userIndicator.className = "user-indicator"
        userIndicator.innerHTML = `
          <span>Halo, ${user.name}!</span>
          <a href="auth/logout.php" class="logout-link">Logout</a>
        `
        loginBtn.parentNode.insertBefore(userIndicator, loginBtn)
      }
    }

    // Update wishlist buttons state jika ada
    this.loadUserWishlistState()
  }

  // FUNGSI BARU: Load wishlist state untuk user yang baru login
  async loadUserWishlistState() {
    try {
      const response = await fetch("api/wishlist.php?action=get")
      const result = await response.json()

      if (result.success && result.wishlist) {
        // Update wishlist button states
        result.wishlist.forEach((productId) => {
          const wishlistBtns = document.querySelectorAll(`[data-product-id="${productId}"]`)
          wishlistBtns.forEach((btn) => {
            btn.classList.add("active")
            const icon = btn.querySelector("i")
            if (icon) {
              icon.className = "fas fa-heart"
            }
          })
        })
      }
    } catch (error) {
      console.log("Could not load wishlist state:", error)
    }
  }

  openAuthModal(type = "login") {
    // Panggil fungsi global yang sudah didefinisikan di header
    if (window.openAuthModal) {
      window.openAuthModal(type)
    }
  }
}

// Global functions for backward compatibility
function showTab(tabName) {
  window.mabarApp.showTab(tabName)
}

function joinWhatsAppCommunity() {
  window.mabarApp.joinWhatsAppCommunity()
}

function scrollToRecommendations() {
  window.mabarApp.scrollToRecommendations()
}

function scrollToCategories() {
  window.mabarApp.scrollToCategories()
}

function toggleWishlist(productId) {
  return window.mabarApp.toggleWishlist(productId)
}

function loadProducts(category) {
  window.mabarApp.loadProducts(category)
}

// Initialize the application
function initializeApp() {
  // Check if user is logged in
  checkUserSession()

  // Initialize search functionality
  initializeSearch()

  // Initialize wishlist functionality
  initializeWishlist()

  // Initialize theme
  initializeTheme()
}

// User session management
async function checkUserSession() {
  try {
    const response = await fetch("api/check-session.php")
    const data = await response.json()

    if (data.success && data.user) {
      currentUser = data.user
      updateUIForLoggedInUser()
    }
  } catch (error) {
    console.log("No active session")
  }
}

function updateUIForLoggedInUser() {
  // Update any UI elements that depend on user login state
  const loginBtns = document.querySelectorAll(".login-btn")
  loginBtns.forEach((btn) => {
    btn.style.display = "none"
  })
}

// Search functionality
function initializeSearch() {
  const searchInput = document.getElementById("searchInput")
  if (!searchInput) return

  let searchTimeout

  searchInput.addEventListener("input", function () {
    clearTimeout(searchTimeout)
    const query = this.value.trim()

    if (query.length >= 2) {
      searchTimeout = setTimeout(() => {
        window.mabarApp.handleSearch(query)
      }, 300)
    }
  })

  searchInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      e.preventDefault()
      const query = this.value.trim()
      if (query) {
        window.location.href = `search.php?q=${encodeURIComponent(query)}`
      }
    }
  })
}

// Wishlist functionality
function initializeWishlist() {
  loadWishlistItems()
}

async function loadWishlistItems() {
  if (!currentUser) return

  try {
    const response = await fetch("api/wishlist.php?action=get")
    const data = await response.json()

    if (data.success) {
      wishlistItems = data.items || []
    }
  } catch (error) {
    console.error("Error loading wishlist:", error)
  }
}

// Theme management
function initializeTheme() {
  const themeToggle = document.getElementById("themeToggle")
  if (!themeToggle) return

  const savedTheme = localStorage.getItem("theme") || "light"
  document.documentElement.setAttribute("data-theme", savedTheme)
  updateThemeIcon(savedTheme)
}

function updateThemeIcon(theme) {
  const themeToggle = document.getElementById("themeToggle")
  if (!themeToggle) return

  const icon = themeToggle.querySelector("i")
  icon.className = theme === "light" ? "fas fa-moon" : "fas fa-sun"
}

// Product Detail Review System
function initializeReviewSystem() {
  const reviewForm = document.getElementById("reviewForm")
  if (!reviewForm) return

  reviewForm.addEventListener("submit", async function (e) {
    e.preventDefault()

    const formData = new FormData(this)
    const submitBtn = this.querySelector('button[type="submit"]')

    try {
      submitBtn.disabled = true
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...'

      const response = await fetch("api/reviews.php", {
        method: "POST",
        body: formData,
      })

      const result = await response.json()

      if (result.success) {
        window.mabarApp.showToastNotification("Review berhasil ditambahkan!", "success")
        this.reset()
        loadReviews() // Reload reviews
      } else {
        window.mabarApp.showToastNotification(result.message || "Gagal menambahkan review", "error")
      }
    } catch (error) {
      console.error("Review error:", error)
      window.mabarApp.showToastNotification("Terjadi kesalahan", "error")
    } finally {
      submitBtn.disabled = false
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Review'
    }
  })
}

async function loadReviews() {
  const reviewsList = document.getElementById("reviewsList")
  if (!reviewsList) return

  const productId = new URLSearchParams(window.location.search).get("id")
  if (!productId) return

  try {
    const response = await fetch(`api/reviews.php?product_id=${productId}`)
    const result = await response.json()

    if (result.success && result.reviews) {
      renderReviews(result.reviews, reviewsList)
    }
  } catch (error) {
    console.error("Error loading reviews:", error)
  }
}

function renderReviews(reviews, container) {
  if (reviews.length === 0) {
    container.innerHTML = `
      <div class="no-reviews">
        <i class="fas fa-comment-slash"></i>
        <h4>Belum ada review</h4>
        <p>Jadilah yang pertama memberikan review untuk produk ini</p>
      </div>
    `
    return
  }

  container.innerHTML = reviews
    .map(
      (review) => `
    <div class="review-item">
      <div class="review-header">
        <div class="reviewer-info">
          <div class="reviewer-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div class="reviewer-details">
            <h4>${review.user_name}</h4>
            <div class="review-rating">
              ${generateStars(review.rating)}
            </div>
          </div>
        </div>
        <div class="review-date">
          ${formatDate(review.created_at)}
        </div>
      </div>
      <div class="review-content">
        ${review.comment}
      </div>
    </div>
  `,
    )
    .join("")
}

function generateStars(rating) {
  let stars = ""
  for (let i = 1; i <= 5; i++) {
    stars += `<i class="fas fa-star ${i <= rating ? "active" : ""}"></i>`
  }
  return stars
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  })
}

// Initialize review system on product detail pages
document.addEventListener("DOMContentLoaded", () => {
  if (window.location.pathname.includes("product-detail.php")) {
    initializeReviewSystem()
    loadReviews()
  }
})

// Global function for requiring authentication
function requireAuth(callback, action = "melakukan aksi ini") {
  if (!window.mabarApp.isLoggedIn()) {
    window.mabarApp.showToastNotification(`Anda harus login untuk ${action}`, "info")
    setTimeout(() => {
      window.mabarApp.openAuthModal("login")
    }, 1500)
    return false
  }
  callback()
  return true
}
