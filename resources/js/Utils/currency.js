export function formatCurrency(value) {
    if (!value) return '';
    let val = value.toString().replace(/\D/g, '');
    val = (Number(val) / 100).toFixed(2).replace('.', ',');
    return 'R$ ' + val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
