<div class="Menu">
    <div class="selection">
        <div class="backBtn">
            <button>
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
        <div class="select">
            <div class="breadcrumbs-menu" name="item" id="item">
                <div class="crumb-title" id="crumb-title">Select Stuff</div>
                <div class="breadcrumbs">
                    <div class="breadcrumb">Cool</div>
                    <div class="breadcrumb">Wow</div>
                </div>
            </div>
        </div>
    </div>
    <div class="crumbs">
        <div class="crumb"> <i class="bi bi-geo-alt-fill"></i>
            Hello World
        </div>
        <div class="crumb"> <i class="bi bi-geo-alt-fill"></i>
            Cool One
        </div>
        <div class="crumb"> <i class="bi bi-geo-alt-fill"></i>
            Wow Too
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".breadcrumbs").hide();
        $(".crumb-title").click(function() {
            $(".breadcrumbs").toggle();
        });
    });
</script>