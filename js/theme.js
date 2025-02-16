// 导航栏用户菜单
const user_set_btn = document.querySelector('.nav-bar .admin img');
const user_set_menu = document.querySelector('.nav-bar .user-set');
if(user_set_btn) {
    function remove_set_menu(e) {
        user_set_menu.classList.remove('user-set-open');
        document.removeEventListener("click",remove_set_menu);
    }
    user_set_btn.addEventListener("click",(e)=>{
        e.stopPropagation()
        if(user_set_menu.classList.toggle('user-set-open')) {
            document.addEventListener("click",remove_set_menu);
        }
    })
    user_set_menu.addEventListener("click",(e)=>e.stopPropagation());
}

const user_set_btn_mb = document.querySelector('.nav-bar-mb .admin img');
const user_set_menu_mb = document.querySelector('.nav-bar-mb .user-set');
if(user_set_btn_mb) {
    function remove_set_menu(e) {
        user_set_menu_mb.classList.remove('user-set-open-mb');
        document.removeEventListener("click",remove_set_menu);
    }
    user_set_btn_mb.addEventListener("click",(e)=>{
        e.stopPropagation()
        if(user_set_menu_mb.classList.toggle('user-set-open-mb')) {
            document.addEventListener("click",remove_set_menu);
        }
    })
    user_set_menu_mb.addEventListener("click",(e)=>e.stopPropagation());
}

// 菜单
const menu_mb_open = document.querySelector('#menu-mb-open');
const menu_mb_close = document.querySelector('#menu-mb-close');
const menu_mb = document.querySelector('.menu-mb');
const menu_mb_mask = document.querySelector('.menu-mb-mask');

menu_mb_open.onclick = function() {
    menu_mb.className = 'menu-mb menu-mb-open';
    menu_mb_mask.className = 'menu-mb-mask menu-mb-mask-block';
}
menu_mb_mask.onclick = function() {
    menu_mb.className = 'menu-mb';
    this.className = 'menu-mb-mask';
}
menu_mb_close.onclick = function() {
    menu_mb.className = 'menu-mb';
    menu_mb_mask.className = 'menu-mb-mask';
}

// 目录树菜单按钮
const post_menu_mb_btn = document.querySelector('.post-menu-mb-btn');
const post_menu_mb = document.querySelector('#article-toc-mb');
if(post_menu_mb && post_menu_mb_btn) {
    post_menu_mb_btn.className = 'post-menu-mb-btn post-menu-mb-btn-on';
    function remove_post_menu(e) {
        post_menu_mb.classList.remove('article-toc-mb');
        document.removeEventListener("click",remove_post_menu);
    }
    post_menu_mb_btn.addEventListener("click",(e)=>{
        e.stopPropagation()
        if(post_menu_mb.classList.toggle('article-toc-mb')) {
            document.addEventListener("click",remove_post_menu);
        }
    })
    post_menu_mb.addEventListener("click",(e)=>e.stopPropagation());
}

// 删除 "发表在"
const comments_meta = document.querySelectorAll('.wp-block-latest-comments__comment-meta');
const comments_meta_fabiao = [];
if(comments_meta) {
    $(comments_meta).each(function(i) {
        comments_meta[i].removeChild(comments_meta[i].childNodes[1]);
    })
}
// 删除评论作者超链接
const remove_comments_author_a = document.querySelectorAll('.wp-block-latest-comments__comment-author');
$(remove_comments_author_a).each(function(i) {
    remove_comments_author_a[i].removeAttribute('href');
})

// 给评论摘要添加超链接
const comments_links = document.querySelectorAll('.wp-block-latest-comments__comment-link');
const comments_excerpt = document.querySelectorAll('.wp-block-latest-comments__comment-excerpt p');
if(comments_links) {
    const comments_links_href = [];
    const comments_links_a = [];
    $(comments_excerpt).each(function(i) {
        comments_links_a[i] = document.createElement('a');
        comments_links_href[i] = comments_links[i].getAttribute('href');
        comments_links_a[i].setAttribute('href',comments_links_href[i]);
        comments_excerpt[i].parentNode.replaceChild(comments_links_a[i],comments_excerpt[i]);
        comments_links_a[i].appendChild(comments_excerpt[i]);
    })
}

// 滚动时隐藏header
var header_element = document.querySelectorAll(".header-hidden");
if(header_element) {
    var headroom = []; 
    $(header_element).each(function(i) {
        // headroom[i] = new Headroom(header_element[i]);
        headroom[i] = new Headroom(header_element[i], {
            "tolerance": 5,
            "offset": 205,
          });
        headroom[i].init(); 
    })
}

// 侧边栏搜索
const sidebar_search_btn = document.querySelector('#primary-sidebar .widget_search button');
if(sidebar_search_btn) {
    sidebar_search_btn.innerHTML = '<span class="iconfont icon-sousuo">';
}

const buttons = document.querySelectorAll('.ripple');
buttons.forEach(button => {
    button.addEventListener('click', function (e) {
        const x = e.clientX;
        const y = e.clientY;

        const buttonTop = e.target.offsetTop;
        const buttonLeft = e.target.offsetLeft;

        const xInside = x - buttonLeft;
        const yInside = y - buttonTop;

        const circle = document.createElement('span');
        circle.classList.add('circle');
        circle.style.top = yInside + 'px';
        circle.style.left = xInside + 'px';

        this.appendChild(circle);

        setTimeout(() => circle.remove(), 500);
    })
})

// 图片预览
const content_p_img = document.querySelectorAll('.post-content .wp-block-image img');
if(content_p_img) {
	const content_p_a = [];
	for(let i=0; i<content_p_img.length; i++) {
		content_p_a[i] = document.createElement('a');
		content_p_img[i].parentNode.replaceChild(content_p_a[i],content_p_img[i]);
		content_p_a[i].appendChild(content_p_img[i]);
        content_p_a[i].setAttribute('href',content_p_img[i].getAttribute('data-original'));
        content_p_a[i].setAttribute('data-fancybox','gallery');
	}
}

const say_p_img = document.querySelectorAll('.say-post-content p img');
if(say_p_img) {
	const say_p_a = [];
	for(let i=0; i<say_p_img.length; i++) {
		say_p_a[i] = document.createElement('a');
		say_p_img[i].parentNode.replaceChild(say_p_a[i],say_p_img[i]);
		say_p_a[i].appendChild(say_p_img[i]);
        say_p_a[i].setAttribute('href',say_p_img[i].getAttribute('data-original'));
        say_p_a[i].setAttribute('data-fancybox','gallery');
        say_p_a[i].className = 'say-img';
	}
}

const say_dt = document.querySelectorAll('.say-post-content .gallery .gallery-item dt');
const say_dt_img = document.querySelectorAll('.say-post-content .gallery .gallery-item dt img');
if(say_dt_img) {
	const say_dt_img_src = []; 
	for(let i=0; i<say_dt_img.length; i++) {
		say_dt[i].setAttribute('href',say_dt_img[i].getAttribute('data-original'));
        say_dt[i].setAttribute('data-fancybox','gallery');
	}
}

// 关于
console.log('%c iFalse %c https://gitee.com/kannafay/ifalse', 'background: linear-gradient(to right, #8183ff, #a1a1f7);color:#fff;border-radius:2px;', '');

// 查询次数，花费时间
console.log(document.querySelector('#queries_num').firstChild.data);

