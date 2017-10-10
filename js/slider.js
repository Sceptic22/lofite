//время в мс анимации скрытия и появления блоков
var timeShow=1000,timeHide=1000;

//общее кол-во блоков,кол-во блоков на одной странице,кол-во страниц,текущая(активная) страница
var countBlocks=0,blocksOnePage=4,countPages=0,currentPage=0;
//сколько уже скрыто блоков(в процессе анимации),начало и конец итераций,
// кол-во скрываемых блоков(если на странице например меньше блоков чем стандарт - крайняя страница)
var countHide=0,start,end,countHiddenAnimation=-1;

$(document).ready(mready);

//начальная инициализация
function mready()
{
    //расчет кол-ва страниц в зависимости от общего кол-ва блоков и кол-ва блоков на одной станице
    countBlocks=$("div.project_container").length;
    countPages=Math.ceil(countBlocks/blocksOnePage);


    //создаем ссылки на страницы
    var link;
    for(var i=0;i<countPages;i++)
    {
        link= $("<a>");
        link.text((i+1).toString());
        link.click(link_click);
        link.attr("href",(i+1).toString());
        link.appendTo($("div[class*='pag_numbers']"));
    }

    //показываем блоки на первой странице
    var array=$("div.project_container");

    //если общее кол-во блоков меньше,чем то что должно быть на одной странице,то
    // отображаем те что есть
    if(blocksOnePage>countBlocks)
        blocksOnePage = countBlocks;


    for(var i=0;i<blocksOnePage;i++)
    {
        array.eq(i).removeClass("phide").css("opacity","1");
    }

    //обновляем активную вкладку
    updateCurrentPage();

}


//при клике по ссылке пагинации
function link_click()
{
    //обнуляем
    countHide=0;

    //если кликнули по уже активной вкладке ничего не делаем
    if(currentPage==parseInt($(this).text())-1)
        return false;

    //получаем номер кликнутой страницы
    currentPage=parseInt($(this).text())-1;

    //получаем все блоки которые сейчас отображены и анимируем их opacity
    var array=$("div.project_container").not("[class*='phide']");
    //кол-во блоков(для того чтобы определить когда они все закончили анимацию)
    countHiddenAnimation=array.length;
    for(var i=0;i<array.length;i++)
    {
        array.eq(i).animate({opacity: 0}, timeHide, changeBlock);
    }

    //обновляем активную вкладку
    updateCurrentPage();

    return false;
}

function changeBlock()
{
    //после того как старые блоки скрылись, ставим им display:none,чтобы не занимали место на странице(и метка для нас)
    $(this).addClass("phide");

    //считаем кол-во уже скрытых блоков
    countHide++;

    //если скрыты все блоки,получаем те блоки,которые необходимо показать и анимируем их появление
    if(countHide==countHiddenAnimation) {
        array = $("div.project_container");
        start = currentPage * blocksOnePage;
        end = start + blocksOnePage;

        //если мы хотим получить блоков по стандарту(сколько должно быть на странице),но их в коллекции меньше,
        // уменьшаем кол-во до того,сколько есть
        //т.е. такое может быть только на последней странице
        if (end > array.length)
            end = array.length;


        for(var j=start;j<end;j++)
        {
            array.eq(j).removeClass("phide").animate({opacity: 1},timeShow,function() {});
        }


    }
}

//обновляем активную вкладку
function updateCurrentPage()
{
    //убираем метку с текущей активной вкладки
    $("div[class*='pag_numbers'] a[class*='active_a']").removeClass("active_a");
    //ставим метку на ссылку,которая по счету совпадает с текущей
    $("div[class*='pag_numbers'] a").eq(currentPage).addClass("active_a");
}
