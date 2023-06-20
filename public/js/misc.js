function showModal(templateID, size = '', modalPrefix = '', callback = []) {
    let modalContent = $(templateID).html();
    let _modalPrefix = modalPrefix;

    if (modalPrefix !== 'modalParentStatic') {
        _modalPrefix = 'modalParent';
    }

    window.parent.$('.modal').modal('hide');
    window.parent.$(`#${_modalPrefix}Content`).html(modalContent);
    window.parent.$(`#${_modalPrefix} .modal-dialog`).attr("class", `modal-dialog modal-dialog-centered ${size ? size : ''}`);

    if (callback) {
        callback.forEach(item => {
            if (window.parent.$(".modal #" + item.ID).length > 0) {
                window.parent.$(".modal #" + item.ID).click(function () {
                    if (typeof window[item.FunctionName] === 'function') {
                        if (callback.Args) {
                            window[item.FunctionName](...callback.Args);
                        } else {
                            window[item.FunctionName]();
                        }
                    }
                });
            }
        });
    }

    window.parent.$(`#${_modalPrefix}`).modal()
}

function showTour() {
    const tour = new Shepherd.Tour({
        defaultStepOptions: {
            classes: 'shadow-lg bg-primary rounded',
            scrollTo: true
        },
        useModalOverlay: true
    });

    // Menu
    tour.addStep({
        id: 'first-step',
        text: '<div class="text-left"><h6 class="text-light mb-3">Navigasi Menu</h6><p class="text-light">Sekarang menu navigasi ada di bagian atas, Anda dapat mengakses transaksi Transfer, Pembayaran & Pembelian, Laporan, Informasi rekening dan Informasi limit di sini.</p></div>',
        attachTo: {
            element: '#navbar-menu',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Lanjut',
                classes: 'bg-light text-primary px-3',
                action: tour.next
            }
        ]
    });

    // Profil
    tour.addStep({
        id: 'second-step',
        text: '<div class="text-left"><h6 class="text-light mb-3">Informasi Akun</h6><p class="text-light">Gunakan infomasi akun untuk mengubah Profile dan Password Anda. Untuk Log out dari IBBIZ Anda juga bisa menggunakan fitur ini.</p></div>',
        attachTo: {
            element: '#navbar-profile',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Lanjut',
                classes: 'bg-light text-primary px-3',
                action: tour.next
            }
        ]
    });

    // Edit beranda
    tour.addStep({
        id: 'third-step',
        text: '<div class="text-left"><h6 class="text-light mb-3">Edit Beranda</h6><p class="text-light">Sekarang Anda bisa mengatur beranda sesuai dengan kebutuhan transaksi yang ingin Anda tampilkan di halaman beranda dengan memilih tombol Edit Beranda di kanan atas halaman.</p></div>',
        buttons: [
            {
                text: 'Finish',
                classes: 'bg-light text-primary px-3',
                action: tour.next
            }
        ]
    });

    tour.start();
}
