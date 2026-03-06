#import "@preview/cades:0.3.1": qr-code

#set text(font: "Avenir")
#set page(margin: 0.75cm)

#let songData = json("./../../storage/app/private/data.json")

#grid(
    columns: (6.3cm, 6.3cm, 6.3cm),
    rows: 6.3cm,
    gutter: 0.3cm,
    /*stroke: (paint: black, thickness: 1pt, dash: "dashed"),*/
    ..songData.map(
        ((front, back)) => {
            front.map(
                ((name, artist, release_year, color, url)) => {
                    grid.cell(
                        inset: 0.4cm,
                        fill: gradient.radial(center: (40%, 40%), luma(0%), luma(20%)
                    ),
                    {
                        place(
                            center + horizon,
                            qr-code(url, width: 4cm)
                        );
                    })
                },
            )
            back.map(
                ((name, artist, release_year, color)) => {
                    grid.cell(
                        inset: 0.4cm,
                        fill: gradient.radial(center: (40%, 40%), rgb(color).lighten(35%), rgb(color).lighten(10%)
                    ),
                    {
                        square(
                            width: 100%,
                            height: 100%,
                            stroke: (paint: rgb(color), thickness: 0.2cm),
                            inset: 0.4cm,
                            {
                                place(
                                    top + center,
                                    stack(
                                        spacing: 0.7em,
                                        text(size: 1.1em, weight: 600, spacing: 60%)[#name],
                                        text(size: 0.8em, style: "italic", fill: black.lighten(20%))[#artist],
                                    )
                                );
                                place(
                                    center + bottom,
                                    text(font: "Apple SD Gothic Neo", size: 4.5em, weight: 700, spacing: 2pt)[#release_year]
                                );

                            }
                        )
                    })
                },
            )
        }
    ).flatten(),
)
