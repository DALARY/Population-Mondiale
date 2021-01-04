function submitForm() {
    document.getElementById('form_criteria').submit();
}
function changeContinent() {
    parent.location.href="population.php?continent="+document.getElementById('continent').value;
}
function changeRegion() {
    parent.location.href="population.php?continent="+document.getElementById('continent').value+"&region="+document.getElementById('region').value;
}
