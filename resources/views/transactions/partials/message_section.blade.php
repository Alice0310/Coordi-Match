<div id="message-section">
    <h4>メッセージ</h4>
    <div id="message-list" class="message-list">
        @foreach($messages as $msg)
            <div class="message-item {{ $msg->user_id === auth()->id() ? 'me' : 'other' }}">
                <strong>{{ $msg->user->nickname }}</strong>
                <p>{{ $msg->message }}</p>

                {{-- 添付写真 --}}
                @if($msg->photos->count() > 0)
                    <div class="message-photos">
                        @foreach($msg->photos as $photo)
                            <img src="{{ asset('storage/'.$photo->photo_path) }}" alt="添付画像" style="max-width:150px; margin:5px;">
                        @endforeach
                    </div>
                @endif

                <span class="time">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function() {
    // ページロード時に一番下へスクロール
    const $list = $('#message-list');
    $list.scrollTop($list[0].scrollHeight);
});
</script>