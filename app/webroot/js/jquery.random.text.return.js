/**
* jQuery.RandomTextReturn
* copyright    http://bl6.jp/
* license    The MIT License
*/
(function($){
$.fn.randomTextReturn = function(options){
	options = $.extend({
		clickButton: "",
		fontSizeBefore: 14,
		fontSizeAfter: 15,
		fontSizeRange: 5,
		fontWeight: "normal",
		fadeSpeed: 2000,
		easingSpeed: 2000,
		animationDelay: 3,
		textBox: 15,
		textBoxPadding: 5,
		color: {
			col1: "#000000",
			col2: "#404040",
			col3: "#808080",
			col4: "#bfbfbf",
			col5: "#60d0ff"
		},
		afterAnimation: function() {}
	}, options);
	
	this.each(function(){
		var element = $(this);
		var elementChild = element.children('div');
		var elementChildNum = elementChild.length;
		var aryText = []; /* Insert text into an array */
		var aryTextLength = []; /* Put in the array the number of text */
		for(var i = 0; i < elementChildNum; i++){
			aryText.push(elementChild.eq(i).text());
			aryTextLength.push(aryText[i].length);
		}
		var maxTextLength = Math.max.apply(null, aryTextLength);
		var elementWidth = (options.textBox + (options.textBoxPadding * 2)) * maxTextLength;
		var elementHeight = (options.textBox + (options.textBoxPadding * 2)) * elementChildNum;
		var aryColor = []
		var colorLength = 0;
		for (var i in options.color) {
			colorLength++;
			aryColor.push(options.color[i]);
		}
		element.width(elementWidth).height(elementHeight).css({position: "relative"});
		elementChild.hide();
		
		function run(){
			$.each(aryText,function(i) { 
				var lineText = aryText[i];
				for(var j = 0; j < lineText.length; j++) {
					var randomColor = Math.floor(Math.random() * colorLength);
					var randomFontSize = Math.floor(Math.random() * options.fontSizeRange);
					var randomTop = Math.floor(Math.random() * (elementHeight - options.textBox - options.textBoxPadding));
					var randomLeft = Math.floor(Math.random() * (elementWidth - options.textBox - options.textBoxPadding));
					var delayRange = Math.floor(Math.random() * options.animationDelay);
					var spanStyle = {
						color: aryColor[randomColor],
						fontSize: options.fontSizeBefore * randomFontSize,
						fontWeight: options.fontWeight,
						position: "absolute",
						top: randomTop,
						left: randomLeft,
						width: options.textBox,
						height: options.textBox,
						lineHeight: options.textBox + "px",
						textAlign: "center",
						display: "block",
						padding: options.textBoxPadding
					}
					$('<span />')
						.text(lineText.charAt(j))
						.css(spanStyle)
						.fadeIn(options.fadeSpeed)
						.delay(500 * delayRange)
						.appendTo(element)
						.animate({
							top: (spanStyle.width+(spanStyle.padding*2)) * (i),
							left: (spanStyle.height+(spanStyle.padding*2)) * (j),
							fontSize: options.fontSizeAfter
						}, options.easingSpeed, options.afterAnimation);
				};
			});
		}
		if(!options.clickButton){
			run();
		}else{
			$(options.clickButton).click(function(){
				element.children('span').hide();
				run();
			});
		}
	});
	return this;
};
})(jQuery);