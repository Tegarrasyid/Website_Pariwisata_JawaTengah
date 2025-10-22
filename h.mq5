//@version=6
indicator("Institutional Activity Analysis", shorttitle="IAA", overlay=false)

// Dashboard Position Input
dashPosition = input.string("Bottom Right", "Dashboard Position", options=["Top Right", "Top Left", "Bottom Right", "Bottom Left", "Center", "Off"])

// Input parameters
volMultiplier = input.float(2.0, "Volume Spike Threshold", minval=1.0, maxval=5.0, step=0.1)
smoothingPeriod = input.int(14, "Smoothing Period", minval=5, maxval=50)
priceThreshold = input.float(1.5, "Price Movement Threshold", minval=0.5, maxval=3.0, step=0.1)

// Visual Settings
dashBgColor = input.color(color.new(#0c0c0c, 80), "Dashboard Background")
dashTextColor = input.color(color.new(color.white, 0), "Dashboard Text")
fontSize = input.string("Small", "Font Size", options=["Tiny", "Small", "Normal"])

// Calculate volume metrics
volumeSMA = ta.sma(volume, smoothingPeriod)
volumeStdDev = ta.stdev(volume, smoothingPeriod)
volumeThreshold = volumeSMA + volumeStdDev * volMultiplier

// Price movement analysis
hlRange = high - low
hlRangeSMA = ta.sma(hlRange, smoothingPeriod)
priceVolatility = hlRangeSMA != 0 ? hlRange / hlRangeSMA : 0

// Smart Money Flow Index (SMFI)
smfi = close > open ? volume * (close - low) / (high - low) : volume * (close - high) / (high - low)
smfiSMA = ta.sma(smfi, smoothingPeriod)

// Accumulation/Distribution
accDist = ta.accdist()
accDistSMA = ta.sma(accDist, smoothingPeriod)
accDistSlope = accDistSMA != 0 ? (accDist - accDistSMA) / accDistSMA * 100 : 0

// Volume spike detection
volumeSpike = volume > volumeThreshold

// Significant price movement
significantMove = priceVolatility > priceThreshold and volume > volumeThreshold

// Plots
plot(smfiSMA, title="Smart Money Flow", color=color.orange, linewidth=2)
plot(accDistSlope, title="Acc/Dist Slope", color=color.purple, linewidth=1)
plotshape(volumeSpike, title="Volume Spike", style=shape.triangledown, location=location.belowbar, color=color.yellow, size=size.small)
plotshape(significantMove, title="Significant Move", style=shape.triangleup, location=location.belowbar, color=color.green, size=size.small)

// Dashboard Text
dashText = ""
if dashPosition != "Off"
    dashText += "IAA METRICS\n\n"
    dashText += "Volume Flow: " + (volume > volumeSMA ? "A" : "") + "\n"
    dashText += "Smart Money: " + (smfiSMA > 0 ? "Bull" : "Bear") + "\n"
    dashText += "Acc/Dist: " + (accDistSlope > 0 ? "Acc" : "Dist") + "\n"
    dashText += "Volatility: " + str.tostring(priceVolatility, "#.##") + "\n\n"
    dashText += "Signals:\n"
    dashText += (volumeSpike ? "Vol Spike " : "Normal ")
    dashText += (significantMove ? "Strong Move" : "Stable")

// Dashboard Position Logic
tablePosition = dashPosition == "Top Right" ? position.top_right :
                dashPosition == "Top Left" ? position.top_left :
                dashPosition == "Bottom Left" ? position.bottom_left :
                dashPosition == "Center" ? position.middle_center :
                position.bottom_right

// Create dashboard table
if dashPosition != "Off"
    var table dashTable = table.new(tablePosition, 1, 1)
    table.cell(
         table_id = dashTable,
         column = 0,
         row = 0,
         text = dashText,
         bgcolor = dashBgColor,
         text_color = dashTextColor,
         text_size = fontSize == "Tiny" ? size.tiny : fontSize == "Small" ? size.small : size.normal
    )

// Heatmap (simple version)
heatmapValue = volume * priceVolatility
heatmapMax = ta.highest(heatmapValue, 100)
heatmapColor = color.new(color.blue, 100 - (heatmapValue / (heatmapMax != 0 ? heatmapMax : 1)) * 100)
plot(heatmapValue, title="Activity Heatmap", color=heatmapColor, style=plot.style_columns, linewidth=4)
