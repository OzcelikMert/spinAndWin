const Api = {
    showErrorMessage(errorCode) {
        let title = "";
        let text = "";
        switch (errorCode) {
            case ErrorCodes.NOT_FOUND:
                title = "Bulunamadı!";
                text = "Girdiğiniz bilgilere göre sonuç alınamadı. Lütfen girilen bilgilerin doğruluğundan emin olunuz."
                break;
            case ErrorCodes.NO_PERM:
                title = "Yetki Yetersiz!";
                text = "Yetkiniz bu işlemi yapmak için yeterli değil. Lütfen destek birimi ile irtibata geçiniz."
                break;
            default:
                title = "Hata!";
                text = "Bilinmeyen bir hata oluştu lütfen destek ekibi ile görüşün."
                break;
        }

        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
        });
    }
}