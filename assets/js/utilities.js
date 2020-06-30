let move  = function ( array, index ) {
    console.log(index)
    return array.concat(  array.splice(0, index ) )
};

export { move }
