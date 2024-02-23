$(function () {
    let $formAdd = document.querySelector("form#addForm");
    let $formEdit = document.querySelector("form#editForm");
    let $buttonExport = document.querySelector("button.exportButton");
    let $imageEditModal = document.querySelector("#editModal img");
    let $table = null;
    let rows = [];
    let rowId = "";

    function initTable(showLoader = false) {
        function getDeleteButtonElement(row) {
            return `<button class="btn btn-danger deleteButton" row-id="${row.itemId}">Sil</button>`
        }

        function getUpdateButtonElement(row) {
            return `<button class="btn btn-warning editButton" row-id="${row.itemId}">Güncelle</button>`
        }

        function getImageElement(row) {
            return `<img class="item-image" alt="${row.itemText}" src="./uploads/${row.itemImage}" />`
        }

        function getImageEmptyElement(row) {
            return `<img class="item-image" alt="${row.itemText}" src="./assets/images/empty.jpg" />`
        }

        if(showLoader){
            $.Toast.showToast({
                "title": "Yükleniyor...",
                "icon": "loading",
                "duration": 0
            });
        }

        $.ajax({
            url: "api/get.php",
            type: "GET",
            data: { query: "OK" },
            success: (res) => {
                var json = JSON.parse(res);
                if (json.status) {
                    rows = json.rows;
                    rows = ArrayList.sort(rows, "itemId", ArrayList.SortTypes.DESC);

                    if ($table == null) {
                        $table = new DataTable('#myTable', {
                            responsive: true,
                            data: rows,
                            columns: [
                                { data: "itemId", orderable: true },
                                { data: "itemImage", orderable: false, render: function (data, type, row) { return row.itemImage ? getImageElement(row) : getImageEmptyElement(row) } },
                                { data: "itemText", orderable: true},
                                { data: "itemProbability", orderable: true},
                                { data: "itemQty", orderable: true},
                                { data: "itemId", orderable: false, render: function (data, type, row) { return getUpdateButtonElement(row) } },
                                { data: "itemId", orderable: false, render: function (data, type, row) { return getDeleteButtonElement(row) } }
                            ],
                            order: []
                        });
                    } else {
                        $table.clear();
                        $table.rows.add(rows);
                        $table.draw();
                    }
                }
                if(showLoader){
                    $.Toast.hideToast();
                }
            },
            error: () => {
                $.Toast.hideToast();
                Swal.fire({
                    icon: 'error',
                    title: "Server Error!",
                    text: 'Please you should contact to admin.'
                })
            }
        })
    }

    $formAdd.addEventListener("submit", function (e) {
        e.preventDefault();

        let data = $(this).serializeObject();

        if (
            Variable.isEmpty(data.itemText) ||
            Variable.isEmpty(data.itemProbability) ||
            Variable.isEmpty(data.itemQty)
        ) {
            Swal.fire({
                icon: 'error',
                title: 'Boş yer bırakma!',
                text: `Lütfen boş yer bırakmayınız.`,
            });
            return false;
        }

        $.Toast.showToast({
            "title": "Ekleniyor...",
            "icon": "loading",
            "duration": 0
        });

        const formData = new FormData(e.target);

        $.ajax({
            url: "api/add.php",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: (res) => {
                $.Toast.hideToast();
                var json = JSON.parse(res);
                if (json.status) {
                    e.target.reset();

                    initTable();

                    Swal.fire({
                        icon: 'success',
                        title: 'Eklendi!',
                        text: `'${data.itemText}' başarıyla eklendi.`,
                    });
                } else {
                    Api.showErrorMessage(json.error_code)
                }
            },
            error: () => {
                $.Toast.hideToast();
                Swal.fire({
                    icon: 'error',
                    title: "Server Error!",
                    text: 'Please you should contact to admin.'
                })
            }
        })
    });

    $formEdit.addEventListener("submit", function (e) {
        e.preventDefault();

        let data = $(this).serializeObject();

        if (
            Variable.isEmpty(data.itemText) ||
            Variable.isEmpty(data.itemProbability) ||
            Variable.isEmpty(data.itemQty)
        ) {
            Swal.fire({
                icon: 'error',
                title: 'Boş yer bırakma!',
                text: `Lütfen boş yer bırakmayınız.`,
            });
            return false;
        }

        $.Toast.showToast({
            "title": "Güncelleniyor...",
            "icon": "loading",
            "duration": 0
        });

        const formData = new FormData(e.target);
        formData.append("itemId", rowId);

        $.ajax({
            url: "api/update.php",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: (res) => {
                $.Toast.hideToast();
                var json = JSON.parse(res);
                if (json.status) {
                    e.target.reset();
                    initTable();

                    $('#editModal').modal("hide");

                    Swal.fire({
                        icon: 'success',
                        title: 'Güncellendi!',
                        text: `'${data.itemText}' başarıyla güncellendi.`,
                    });
                } else {
                    Api.showErrorMessage(json.error_code)
                }
            },
            error: () => {
                $.Toast.hideToast();
                Swal.fire({
                    icon: 'error',
                    title: "Server Error!",
                    text: 'Please you should contact to admin.'
                })
            }
        })
    });

    $(document).on("click", "button.deleteButton", async function (e) {
        let row = ArrayList.find(rows, $(this).attr("row-id"), "itemId");

        let swal = await Swal.fire({
            title: 'Emin misin?',
            text: `Bu '${row.itemText}' verisini silmek istediğinizden gerçekten emin misiniz?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes!',
            cancelButtonText: "No"
        })

        if (swal.value) {
            $.Toast.showToast({
                "title": "Siliniyor...",
                "icon": "loading",
                "duration": 0
            });

            $.ajax({
                url: "api/delete.php",
                type: "POST",
                data: { itemId: row.itemId },
                success: (res) => {
                    $.Toast.hideToast();

                    var json = JSON.parse(res);

                    if (json.status) {
                        initTable();
                    } else {
                        Api.showErrorMessage(json.error_code)
                    }
                },
                error: () => {
                    $.Toast.hideToast();
                    Swal.fire({
                        icon: 'error',
                        title: "Server Error!",
                        text: 'Please you should contact to admin.'
                    })
                }
            })
        }
    });

    $(document).on("click", "button.editButton", function (e) {
        let row = ArrayList.find(rows, $(this).attr("row-id"), "itemId");

        let $form = $("#editForm");

        $form.find("input[name='itemText']").val(row.itemText);
        $form.find("input[name='itemProbability']").val(row.itemProbability);
        $form.find("input[name='itemQty']").val(row.itemQty);
        $imageEditModal.src = row.itemImage ? `./uploads/${row.itemImage}` : "./assets/images/empty.jpg";
        $imageEditModal.alt = row.itemName;

        rowId = row.itemId;

        $("#editModal").modal("toggle");
    });

    $buttonExport.addEventListener("click", async function (e) {
        function exportJson() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, "0");
            const day = String(today.getDate()).padStart(2, "0");
          
            const fileName = `spinAndWin-${year}${month}${day}-items-${rows.length}.json`;

            const blob = new Blob([JSON.stringify(rows)], { type: "application/json" });
            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        if(rows.length == 0){
            Swal.fire({
                icon: 'error',
                title: "Hiç veri yok!",
                text: 'Dışarı aktarmak için kayıtlı hiç veri yok! Lütfen önce veri ekleyiniz.'
            });
            return false;
        }

        let swal = await Swal.fire({
            title: 'Emin misiniz?',
            text: `Eğer dışarı aktarma işlemini onaylarsanız eklenmiş olan tüm veriler silinecektir! Tüm verileri dışarı aktarmak istediğinizden emin misiniz? `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes!',
            cancelButtonText: "No"
        })

        if (swal.value) {
            $.Toast.showToast({
                "title": "Dışarı Aktarılıyor...",
                "icon": "loading",
                "duration": 0
            });

            exportJson();

            $.ajax({
                url: "api/deleteAll.php",
                type: "POST",
                data: { query: "OK"},
                success: (res) => {
                    $.Toast.hideToast();

                    var json = JSON.parse(res);

                    if (json.status) {
                        initTable();
                    } else {
                        Api.showErrorMessage(json.error_code)
                    }
                },
                error: () => {
                    $.Toast.hideToast();
                    Swal.fire({
                        icon: 'error',
                        title: "Server Error!",
                        text: 'Please you should contact to admin.'
                    })
                }
            })
        }
    });

    initTable(true);
});