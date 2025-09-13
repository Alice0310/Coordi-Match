$(document).ready(function () {
    // ドロップダウン開閉
    $('.dropdown > a').on('click', function (e) {
      e.preventDefault();
      $(this).siblings('.dropdown-menu').toggle();
    });
  
    // 外側クリックで閉じる
    $(document).on('click', function (e) {
      if (!$(e.target).closest('.dropdown').length) {
        $('.dropdown-menu').hide();
      }
    });
  });
  