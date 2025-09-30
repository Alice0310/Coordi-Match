<div class="message-form">
    <form id="messageForm" action="{{ route('trades.sendMessage', $trade->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <textarea name="message" rows="2" placeholder="メッセージを入力..."></textarea>

        <label for="photoInput" class="custom-file-label">📷 写真を選択</label>
        <input type="file" id="photoInput" name="photos[]" multiple accept="image/*" style="display:none;">

        <div id="fileCount" style="margin-top: 5px; font-size: 14px; color: #666;"></div>
        <button type="submit" class="btn btn-primary">送信</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function() {
    const $list = $('#message-list');

    // メッセージ送信
    $('#messageForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    let photosHtml = '';
                    if (res.photos && res.photos.length > 0) {
                        res.photos.forEach(url => {
                            photosHtml += `<img src="${url}" style="max-width:150px; margin:5px;">`;
                        });
                    }

                    let html = `
                        <div class="message-item ${res.user_id === {{ auth()->id() }} ? 'me' : 'other'}">
                            <strong>${res.user}</strong>
                            <p>${res.message ?? ''}</p>
                            ${photosHtml}
                            <span class="time">${res.time}</span>
                        </div>
                    `;
                    $('#message-list').append(html);

                    // 下にスクロール
                    $list.scrollTop($list[0].scrollHeight);

                    // フォームリセット
                    $('#messageForm textarea').val('');
                    $('#messageForm input[type=file]').val('');
                    $('#fileCount').text('');
                }
            },
            error: function(xhr) {
                alert("送信エラー: " + xhr.responseText);
            }
        });
    });

    // 写真選択数カウント
    $('#photoInput').on('change', function() {
        const count = this.files.length;

        if (count > 5) {
            alert('写真は最大5枚までです');
            $(this).val('');
            $('#fileCount').text('');
            return;
        }

        $('#fileCount').text(count > 0 ? `選択中 ${count}ファイル` : '');
    });
});
</script>