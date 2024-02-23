<div id="editModal" class="modal fade bd-example-modal-lg">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Güncelleme Ekranı</h5>
            </div>
            <div class="justify-content-center text-center p-3">
                <img class="item-image" src="./assets/images/empty.jpg" alt="empty image">
            </div>
            <?php 
                $formId = "editForm";
                include "components/pages/panel/add.php"; 
            ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancelModalButton" data-dismiss="modal">İptal</button>
            </div>
        </div>
    </div>
</div>