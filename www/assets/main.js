/**
 * validace registracniho formulare pomoci javascriptu
 */
$('.submit').on('click', function (e) {
  const name = $('.name');
  const password = $('.password');
  const repassword = $('.repassword');

  if ($(name).val() === '') {
    alert('Vypln jméno');
    e.preventDefault();
    return false;
  }
  if ($(password).val() === '') {
    alert('Vypln heslo');
    e.preventDefault();
    return false;
  }
  if ($(repassword).val() === '') {
    alert('Vypln heslo znovu');
    e.preventDefault();
    return false;
  }

  if ($(password).val() !== $(repassword).val()) {
    alert('Hesla se neshoduji');
    e.preventDefault();
    return false;
  }

});
/**
 * nezbedny napis, zmena stylu v css
 */
$('.footer-text').on('mouseover', function () {
  if (!$(this).hasClass('troll')) {
    $(this).addClass('troll');
    $(this).addClass('troll-1');
    return;
  }

  if ($(this).hasClass('troll-1')) {
    $(this).removeClass('troll-1');
    $(this).addClass('troll-2');
    return;
  }

  if ($(this).hasClass('troll-2')) {
    $(this).removeClass('troll-2');
    $(this).addClass('troll-3');
    return;
  }

  if ($(this).hasClass('troll-3')) {
    $(this).removeClass('troll-3');
    $(this).addClass('troll-4');
    return;
  }

  if ($(this).hasClass('troll-4')) {
    $(this).removeClass('troll-4');
    $(this).addClass('troll-1');
    return;
  }


});