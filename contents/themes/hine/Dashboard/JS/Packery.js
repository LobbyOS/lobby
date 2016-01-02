/*
 *  jquery.tilewall - v0.0.1
 *  A jQuery Plugin positioning differently sized tiles in a responsive grid.
 *  http://martinwilmer.com/tilewall
 *
 *  Made by Martin Wilmer
 *  Under MIT License
 */
// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {
	"use strict";

		// undefined is used here as the undefined global variable in ECMAScript 3 is
		// mutable (ie. it can be changed by someone else). undefined isn't really being
		// passed in so we can ensure the value of it is truly undefined. In ES5, undefined
		// can no longer be modified.

		// window and document are passed through as local variable rather than global
		// as this (slightly) quickens the resolution process and can be more efficiently
		// minified (especially when both are regularly referenced in your plugin).

		// Create the defaults once
		var pluginName = "tileWall",
			defaults = {
				debounceTime: 100,
				debug: 0,
				hideNavTilesOnViewportWidthSmallerThan: 0,
				maxNumCols: 12,
				minNumCols: 3,
				minTileWidth: 130,
				shrinkTilesOnViewportWidthSmallerThan: 0,
				tileClassName: "tile",
				tileMargin: 2,
				transition: 0 // use (1) or don't use (0) css transitions
			};

		var tileDimensions = {},
			tiles = [],
			numCols = 0,
			numRows = 0,
			overallTileSize = 0,
			tileGrid = [],
			properPositionAllTilesPossible,
			self;

		// The actual plugin constructor
		function Plugin ( element, options ) {
				this.element = element;
				// jQuery has an extend method which merges the contents of two or
				// more objects, storing the result in the first object. The first object
				// is generally empty as we don't want to alter the default options for
				// future instances of the plugin
				this.settings = $.extend( {}, defaults, options );
				this._defaults = defaults;
				this._name = pluginName;
				this.init();
				this.reformatTiles();

				// hack: tileWallWrapper has no initial height
				this.reformatTiles();

				// hack: scrolling in mobile browsers triggers resize event sometimes
				var cachedWidth = $(window).width();

				$(window).resize(
					this.debouncer(function () {
						if ($(window).width() !== cachedWidth) {
							cachedWidth = $(window).width();
							self.reformatTiles();
						}
					}, this.settings.debounceTime)
				);
		}

		// Avoid Plugin.prototype conflicts
		$.extend(Plugin.prototype, {
				init: function () {
					// Place initialization logic here
					// You already have access to the DOM element and
					// the options via the instance, e.g. this.element
					// and this.settings
					// you can add more functions like the one below and
					// call them like so: this.yourOtherFunction(this.element, this.settings).

					self = this;

					if (this.settings.debug) {
						this.debugLog("settings:\n"+JSON.stringify(this.settings, null, 2));
					}

					if (this.settings.debug && this.settings.shrinkTilesOnViewportWidthSmallerThan) {
						var msg = "Automatically shrinking tiles because viewport width is smaller than ";
						msg += this.settings.shrinkTilesOnViewportWidthSmallerThan+"px disregarding the fix positioning of tiles.";
						this.debugLog(msg);
					}

					this.setTileWallPositionRelative();
				},

				getTileWallWrapperWidth: function () {
					var viewPortWidth = $(window).outerWidth(),
						tileWallWrapperWidth = $(this.element).width();
					return (viewPortWidth > tileWallWrapperWidth) ? tileWallWrapperWidth : viewPortWidth;
				},

				calculateNumCols: function () {
					numCols = this.settings.maxNumCols;

					while (((this.getTileWallWrapperWidth() - numCols*2*this.settings.tileMargin) / numCols) < this.settings.minTileWidth) {
						numCols--;
						if (numCols === this.settings.minNumCols) {
							break;
						}
					}
				},

				calculateIdealNumRows: function () {
					numRows = Math.ceil(overallTileSize / numCols);
				},

				calculateTileDimensions: function () {
					tileDimensions.width = tileDimensions.height = (this.getTileWallWrapperWidth() - numCols*2*this.settings.tileMargin) / numCols;
				},

				setTileWallPositionRelative: function () {
					$(this.element).css("position", "relative");
				},

				setTileWallWrapperHeight: function () {
					$(this.element).css("height", numRows * (tileDimensions.height + (this.settings.tileMargin * 2)) + "px");
				},

				setTileDimensions: function () {
					overallTileSize = 0;
					tiles = [];

					var tile,
						tileId = 1,
						tileElements = $(this.element).find("."+this.settings.tileClassName),
						self = this;

					if (tileElements.length === 0 && this.settings.debug) {
						this.debugLog("No DOM elements with class '"+this.settings.tileClassName+"' to use as tiles found.");
						return false;
					}

					tileElements.each(function() {
						tile = $(this);

						// hide nav tiles on small viewports
						if (tile.data("nav") && self.settings.hideNavTilesOnViewportWidthSmallerThan &&
							self.getTileWallWrapperWidth() < self.settings.hideNavTilesOnViewportWidthSmallerThan) {
							tile.css("display", "none");
							return true;
						}

						var width = parseInt(tile.data("width")) ? parseInt(tile.data("width")) : 1,
							height = parseInt(tile.data("height")) ? parseInt(tile.data("height")) : 1,
							positionX = parseInt(tile.data("position-x")) ? parseInt(tile.data("position-x")) : undefined,
							positionY = parseInt(tile.data("position-y")) ? parseInt(tile.data("position-y")) : undefined;

						// automatically shrink tiles on small viewports
						if (self.settings.shrinkTilesOnViewportWidthSmallerThan &&
							self.getTileWallWrapperWidth() < self.settings.shrinkTilesOnViewportWidthSmallerThan) {
							width = 1;
							height = 1;
						}

						// css style for tiles
						tile.css("position", "absolute");
						tile.css("display", "block");
						tile.css("width", (tileDimensions.width * width) + ((width - 1) * self.settings.tileMargin * 2) + "px");
						tile.css("height", (tileDimensions.height * height) + ((height - 1) * self.settings.tileMargin * 2) + "px");
						tile.css("margin", self.settings.tileMargin + "px");
						if (self.settings.transition) {
							tile.css("transition", "0.2s ease-out");
						}

						// add tiles (tile objects) to array
						tiles[tileId] = {
							ref: tile,
							width: width,
							height: height,
							size: width * height,
							priority: tile.data("priority") ? tile.data("priority") : 0, // 1..5
							positionX: positionX,
							positionY: positionY,
							nav: tile.data("nav") ? true : undefined
						};

						overallTileSize += width * height;

						tileId++;
					});
				},

				sortTilesBySizeAndCurrentness: function () {
					var sortedTiles = [];

					// sort by size (descending)
					for (var i = 1; i < tiles.length; i++) {
						if (tiles[i].nav !== undefined || tiles[i].positionX !== undefined) {
							continue;
						}
						for (var j = 0; j < tiles.length; j++) {
							if (sortedTiles[j] === undefined ||
								(tiles[i].priority > sortedTiles[j].priority && tiles[i].size === sortedTiles[j].size) ||
								(tiles[i].size > sortedTiles[j].size && tiles[i].priority === sortedTiles[j].priority))
							{
								sortedTiles.splice(j, 0, tiles[i]);
								break;
							}
						}
					}

					// put navigation tiles in front (if not fix positioned)
					for (i = 1; i < tiles.length; i++) {
						if (tiles[i].nav === true && tiles[i].positionX === undefined) {
							sortedTiles.splice(0, 0, tiles[i]);
						}
					}

					// put tiles with fix position in front
					for (i = 1; i < tiles.length; i++) {
						if (tiles[i].positionX !== undefined) {
							sortedTiles.splice(0, 0, tiles[i]);
						}
					}

					// hack: avoid array starting with index 0 instead of 1
					var arr = [];
					for (i = 0; i < sortedTiles.length; i++) {
						arr[i + 1] = sortedTiles[i];
					}

					tiles = arr;

					if (this.settings.debug) {
						var sortedTilesString = "";
						for (var k = 0; k < sortedTiles.length; k++) {
							sortedTilesString += "priority: " + sortedTiles[k].priority+ " | ";
							sortedTilesString += "size: " + sortedTiles[k].size+ " | ";
							sortedTilesString += "positionX: " + sortedTiles[k].positionX+ " | ";
							sortedTilesString += "positionY: " + sortedTiles[k].positionY+ " | ";
							sortedTilesString += "nav: "+ sortedTiles[k].nav+"\n";
						}
						this.debugLog("sorted tiles:\n"+sortedTilesString);
					}
				},

				reformatTiles: function () {
					this.calculateNumCols();
					this.calculateTileDimensions();
					this.setTileDimensions();
					this.sortTilesBySizeAndCurrentness();
					this.calculateIdealNumRows();
					this.setTileWallWrapperHeight();

					if (this.settings.debug) {
						this.debugLog("initial overallTileSize: " + overallTileSize);
						this.debugLog("initial numRows: " + numRows);
						this.debugLog("initial numCols: " + numCols);
					}

					var numTries = 0;
					properPositionAllTilesPossible = false;

					while (!properPositionAllTilesPossible) {
						numTries++;
						tileGrid = this.initTileGridArray();

						properPositionAllTilesPossible = true;
						$.each(tiles, this.placeTile);

						// increase number of rows
						if (numTries > 50) {
							numTries = 0;
							numRows++;
							this.setTileWallWrapperHeight();

							if (this.settings.debug) {
								this.debugLog("increased number of rows: " + numRows);
							}
						}
					}

					if (this.settings.debug) {
						this.debugLog("number of tries to position all tiles properly: " + numTries);
					}
				},

				// called in $.each loop
				placeTile: function(index, element) {
					var tilePlaced,
						randX,
						randY,
						occupiedGridCoordinates,
						total,
						i,
						loopCount;

					// hack: skip window ref in array
					if (index === 0) {
						return;
					}

					tilePlaced = false;
					loopCount = 0;
					do {
						loopCount++;

						if (loopCount > 200) {
							if (element.ref !== undefined) {
								properPositionAllTilesPossible = false;
							}
							break;
						}

						// prevent tiles to be fix positioned on x-coordinates exceeding "numCols"
						if (element.positionX + element.width - 1 > numCols) {
							element.positionX = undefined;
						}

						// fix positioning (tiles with data-position-x="" and data-position-y="")
						if (element.positionX !== undefined) {
							// top-left-coordinates of the tile
							occupiedGridCoordinates = [{x: element.positionX, y: element.positionY}];
							// all other coordinates of the tile
							occupiedGridCoordinates = self.addTileSurfaceCoordinates(this, occupiedGridCoordinates);

							// sum up values in occupiedGridCoordinates to ensure that
							// the tile is not going to be positioned on already occupied coordinates
							total = 0;
							for (i = 0; i < occupiedGridCoordinates.length; i++) {
								total += tileGrid[occupiedGridCoordinates[i].y][occupiedGridCoordinates[i].x];
							}
						}

						// randomize positioning
						else {
							randX = Math.floor((Math.random() * (numCols - (element.width - 1))) + 1);

							// try to place tiles at higher positions first
							if (loopCount < 25) {
								randY = Math.floor((Math.random() * (Math.ceil(numRows / 2) - (element.height - 1))) + 1);
							} else if (loopCount < 50) {
								randY = Math.floor((Math.random() * (Math.ceil(numRows / 1.6) - (element.height - 1))) + 1);
							} else if (loopCount < 75) {
								randY = Math.floor((Math.random() * (Math.ceil(numRows / 1.2) - (element.height - 1))) + 1);
							} else {
								randY = Math.floor((Math.random() * (numRows - (element.height - 1))) + 1);
							}

							// top-left-coordinates of the tile
							occupiedGridCoordinates = [{ x: randX, y: randY }];
							// all other coordinates of the tile
							occupiedGridCoordinates = self.addTileSurfaceCoordinates(this, occupiedGridCoordinates);

							// sum up values in occupiedGridCoordinates to ensure that
							// the tile is not going to be positioned on already occupied coordinates
							total = 0;
							for (i = 0; i < occupiedGridCoordinates.length; i++) {
								total += tileGrid[occupiedGridCoordinates[i].y][occupiedGridCoordinates[i].x];
							}
						}

						// if total === 0 the
						if (total === 0) {
							$.each(occupiedGridCoordinates, function () {
								tileGrid[this.y][this.x] = index;
							});

							// absolute positioning of the tiles using css
							element.ref.css("left", (occupiedGridCoordinates[0].x - 1) * (tileDimensions.width + (self.settings.tileMargin * 2)) + "px");
							element.ref.css("top", (occupiedGridCoordinates[0].y - 1) * (tileDimensions.height + (self.settings.tileMargin * 2)) + "px");

							tilePlaced = true;
						}

					} while (!tilePlaced);
				},

				initTileGridArray: function () {
					tileGrid = [];
					for (var i = 1; i <= numRows; i++) {
						tileGrid[i] = [];

						for (var j = 1; j <= numCols; j++) {
							tileGrid[i][j] = 0;
						}
					}
					return tileGrid;
				},

				addTileSurfaceCoordinates: function (tile, occupiedGridCoordinates) {
					var i, j;

					// coordinates of horizontal parts
					if (tile.width > 1) {
						for (i = occupiedGridCoordinates[0].x + 1; i < occupiedGridCoordinates[0].x + tile.width; i++) {
							occupiedGridCoordinates.push({
								y: occupiedGridCoordinates[0].y,
								x: i
							});
						}
					}

					// coordinates of vertical parts
					if (tile.height > 1) {
						for (i = occupiedGridCoordinates[0].y + 1; i < occupiedGridCoordinates[0].y + tile.height; i++) {
							occupiedGridCoordinates.push({
								y: i,
								x: occupiedGridCoordinates[0].x
							});
						}
					}

					// coordinates of horizontal/vertical parts
					if (tile.width > 1 && tile.height > 1) {
						for (i = occupiedGridCoordinates[0].y + 1; i < occupiedGridCoordinates[0].y + tile.height; i++) {
							for (j = occupiedGridCoordinates[0].x + 1; j < occupiedGridCoordinates[0].x + tile.width; j++) {
								occupiedGridCoordinates.push({
									y: i,
									x: j
								});
							}
						}
					}

					return occupiedGridCoordinates;
				},

				debouncer: function (func , _timeout) {
					var timeoutID,
						timeout = _timeout || 200;

					return function() {
						var scope = this,
							args = arguments;
						clearTimeout( timeoutID );
						timeoutID = setTimeout(function () {
							func.apply(scope , Array.prototype.slice.call( args ));
						}, timeout);
					};
				},

				debugLog: function (msg) {
					console.log("["+this._name+" debug] "+msg);
				}
		});

		// A really lightweight plugin wrapper around the constructor,
		// preventing against multiple instantiations
		$.fn[ pluginName ] = function ( options ) {
				return this.each(function() {
						if ( !$.data( this, "plugin_" + pluginName ) ) {
								$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
						}
				});
		};

})( jQuery, window, document );
