<?php
require_once 'db.php';

// Embedded fallback bike image (Pearl White Click 125i) — always shows even if DB has no image
define('FALLBACK_BIKE_IMG', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAQFBQkGCQkJCQkKCAkICgsLCgoLCwwKCwoLCgwMDAwNDQwMDAwMDw4PDAwNDw8PDw0OERERDhEQEBETERMREQ0BBAQECAYIBwgIBwgGCAYICAgHBwgICQcHBwcHCQoJCAgICAkKCQgIBggICQkJCgoJCQoICQgKCgoKCg4QDg4Od//CABEIA1AE2gMBIgACEQEDEQH/xAD2AAEBAAMBAQEAAAAAAAAAAAAAAQIEBQMGBxAAAgICAQIEBQIFBAMBAQAAAQIAAwQREgUTECAhMRQVIjBQQEEWIzJCYAYzNHAkQ1FSYREAAAQCBggDBgQEBgMAAAAAAAECEQMEBRASIDBABgchMTVRdbQTQVAUFSIyUmEWQnGRI1NggSUzQ5KhsSRUghIAAQMABgYIBAUDAwQDAAAAAQACERASITFBUQMTIGFxkSIwMlJggaGxQEJi4XCSwdHwI1ByFDOiBFNj8YCQshMBAAIBAgQFBAMBAQEAAAAAAQARITFBEFFhcSAwgZGhQLHR8FDB8eFgcP/aAAwDAQACAQMBAAAC+KCQABYCFBYBYAAAAAAoJSAAogAAAFxlosAAAUIAAAABZSWUgAAUsRYAAAAAAAAAACwLAAAAFESpZUoAALALAACwFIACwAAAFgWACwGvs63H4+wOx2AIv1B8u+0L8VftCfFzY1ygAAAAAlAAFBAAAAKCSpRCiwAACwACwAoIBZSAAABQQAAsAAAAAAFQAAsAAAJSjGgSkpYABZYAAFgBYAAAAAAAKCLBYDX2Nfj8jYHY66UdH9N4m6vy2HYpxsNXkCUhYFhLaYgIWhAAAAAABSACVLCiwFSkAACULCwLCywAWAAAAFBACiAKIFBAUApIAAAAJZbCyCyqBAAWwQAAAAAAAsBBffc89vmOmmfMdMcx0y8x0hzXUHLb2jnq0ZeTX2Nfj8j3HY67e0fsT6jQ0tpeR5eu3Hb/ACn9k/Mq5CVPsNL11l2PbRzNbpc71ON4evkgqggqwIAAAKRYAFxlosjd2ZeS3egcJ090+fuzlPTVdlhucadnj5a+Nlz1iwHVXlOpqpqOp4LpV104930vPt6GTm3t9ienxd7XEmFFxAN/el4LfhpOpq1qNntTL519UuXyl7HGYUMQI9+xLwHS9jjt3pnz962uaDY+3r4B1fD3mi9/DyUYgDehpOlqGvenmvId7hkvZ7CfG36PdPjnt4llgBLO0ael1vY4VRd7c09nW7mTXTLYeOcyzGPtMcdP01NnY5e3fP15nU5eWuHtz2vs63H4+xDsdh2+IP17D4z6o88fj/BfsPzrMbrqbRwZ29w+Yy3/AFOZ6b3KNnj9TloAAAAACggAASxSBXU9+PnMu748hL1/TiQ3+r876L+q38txZfpH5Xs6181hKKna4pPr+fwZH0/j8+rf7Xyw7HjzUev1Pye69e9t/K4z06PB2de+IXCwL0uZF714A+p1/nxu73EzX6C/Pr6dDlZ4TzoYpYbfZ+bS9va+bH0vjwCdZyC7P6T+X+2Ofe5/PNz20/TDLULL5AbPb+bh9D5cSL9Ln8vU+n+awL9P0/h4fonl8GPXXEssAJ3+DTp7/OwNBKbmxr7mt3PHHYuPpr+vpFgx9sNbcxz19HdnrcPPldXlZ6dHtoNfZ1uPxvcdjsAen6t+S7J+lY/n9l+/nwONdP57a0kzYjNhVy88oliLQACFCACFAAjKVACwBlgmWTEZMRlcYZMaBcYFoQAAAABKAAAAAAFQsUgEsKSWyglAsAJVBAKCASxbLEoFQAEWpUAssAABAsX12tGYe/QvPT032gTfc8dBzx0ZoDZ1jPwqW+bX99fj8jYHY65YPofnvU2uz3/jV+g4fV9TV+b+qhhobHUPi/tfn9kfO+viUIBKKCAAoIALKgFWQAAAAAAhQRSggAAAEoqyoikAAlKCAUEAli2WxjQlECgAAAAAAIoLACywFMVqwJUFlgWEoFglSygFgCWLQhYAoQ19jX5HG2B2OwAAACghRAAAAFgAAAALLABYqABYAFgAAKRYAAoIAAAsBYAApSRRFEAAEsUJQpZFgAAUQAAApCkUJYFCWApKEABYktKRRJkIsKQqCoBRKJQiwqC62xr8fj7Fl7PXgAKQAAqCoAFgLAAAAAJbACwAAAAAAAlAUEAsAAAFBLAWFBBRKECwBCiFYrQgUAAAAAAIUAFQAWWAFlgoQQlKIUhQgFllAAAAAAAAFDW2dbj8fZldjsQAAAAAKCAAAJYtCAIylY2FFgABYAACFIKKCAoAIACggKABFAIKQAKQUQsgstgBAl2V1XT5hlKQAokVYsRZRKABFsUSiwgAlIqgQsAAAUEAAsACVFoQCwLr++vx+PsDsdgAAAAAAAAACUAEqWyUCxKAAAAAAFCALAAAAACywJVBLKIFCQKAsokqUQosJRLAUnt4+pfDd0yz26hxr9nvy/n1/R98/J5+lfOnzF2MLPW+BfEiULFQFiVKQMpbIAsAAJQBUyiFgWBYAAACkWBYNfY1+Px9gdjsAAAAAFgAWAAARLcVoAsAAoIAAFBBSWCywAAAAsAAAAAACwAKCFiBQsSwmUol2DVvWzXje2ximLPzX9G6v5L9fH12fl7GNUmrt+R85+efr358cTH38qxSpQBEqLlACwBYFlEACwCgCAAAALACkFgAutsa/H5GwOx1wAAAAAAAAhLVQAsAAoICywAAWUgAAFgAAAAFI+kL827XJTzAAAC2CABKIUWfQYbHJl3HIxOy5EOt5c3Ct5pe5PPq9w+Pz6GkYbPO9D6L7n8m3Y/V8vnPoKPPTjc4X0HMPzeb3OrDy2vJPOLKQLLYAUJYWBUAAACwWAAAAAAAAAA19nX4/H9x2OwAAABFKlhYRVEsAAUCSgAAli0IAAABAtCAAAAQK3NPdPotvmyNzHKHJ1+lhXC8vteEceenmlQVKBKIUWAbHhIp1OnHzfR7uieU8emanvt8Y7OtyvA2/DQxrJiFzhfq/ksj9f8AgNLuRwul811q9OVv6UZ9rg7po+f0u0fGu5yrPC5YgpAAJSxQCAAWAlLKIAWAAAAAAo19jX4/I9x2OuAAsAQUsWCwAiVSUAFgKsCJSpQCAFgAAKQChAAFhASt4y7PEL93qcnZOdwPuPkjU2MsTp+HM2Dww7XgnMZYixKpYAlhccsTtcvxyLjYTLEu3rQZYZwjLM8aEWAGWXnTLLyyPfywkZEq+3hU6/W+RS/VcHXxSCgAClgSgSwAAAAAKIUiwAAAFJr7Ovx+P7q6/ZiwAJQCKQKsAAABYKgAlCwAAFgKItMVEZQihLAAACAbGv6HV5P3XzMvM7PFyrrc36XVOd5+lPCZyJnnjXrrZZHOdjmJ5glBAAUEogGxr5n6L6+3Rl4fM+z5h8hrd36g/Jtb9j4x+aZdbn15ZZ08sdjwFZmPn0c05brcmCqsQoCwFICywssAUEVBYACwLAUiwWCglQqUa2zr8fke47HXAAAAEKgWAFqVACwEAVUilqWolQooAlKgAAsUxqpjPTBYRABD6rt/D/oC/AaX6Jx4+U+r0Oya/wA99z8ycz28Mq9J7eB6YUWekNTy3sTSbuueKkiwSwsCwKRfqvufyf8AUI3PLP0r4/X6nz59f0vjepHa+M+3wr8jn6V8CauOOZ5Zb3Yj53cYne4+fYPjXe4dYFQBUAFmQlLFEoFElkkFAAAAADIlApYomttavH4/uOx2AAAG1qiwNrx86T38R6eQemXiHr5D185ke/nglxzxWRkXHKjb8PITY8Ke2vniZedGfr4iemEJnjinr4h6POj0wkfTfS/B9mZ307vGX6rgaX1lw/M/P7L5Ay8GZpTY162NnV9o+m+e+0+ON36P4j6qvqNCdePkegyLJTx+B/RPijnistjXyLq+vknt4+vkQLbKnT+6/P8A7+X2zxzPTR2+ePlul8aZ4+voY+3X+hPl/T7DI+M8/rNc+afT+58Rr/deB8Tj935HxN+v5x866vLpKRZVyBQAJYiEuNQpSTKISkzxluYCgIACksMZnijW2dfj8f3HY7ABYAFgsFSiAAAAyxpkhQFlAABEBQKABLCTImDNLgsL1OVkfUfR/EdWWu3yz6Xh8/7FPyvq/S8w4HO+y+YrxIfYX5T1Nf6D5roWfZ9j53rS7/A+l+POh6/O/RD576XnHwtlrOQTz9fIyY5nmC5Sp6/on5z+jS7HphmeWn78w+X5mWNZY2p9P9V8P9rL6r7ni28TQw6eRzfTd1iYeg0vHpapyub9LD878vsflDwzxysyCgEolRMcsRKoskSy2gQLnhlFACBalCBccoY6+zrcfj+47HYBAFgABQAAQARVlGWJMkqrKAJYALAUAQAAsQCJZFhepyvaX6P6T4rqzLa9ep8+fX/P6P1dx+I8+x8mauMtmWPp5rPaSPqu78d9Qdrn+nufl/Q5tPutj8+7Zx/HoatecsHn6eZjnhmYwLljmZfoP5/9UfRZeWUa/wAp9L8EeFirsee6Z/oXwn20b1wyrLG4xfP43jn6Vl+Zb59w+a7x6Mdgy9fX0NThfUeJ+Ref6F8AYiiC2IbWt1fPe1vPb9PLd42e3h66F0uryl2Njxszun1ubZ5beWxLp+O9rXDZy9fLz3NXx3tH25wZ64pFEqDV29Tj8f3HY7AAICpRFIAACgiUAAAW4jJjVqUAWUELEKiKYmWMpYtkAlhfXy9V72rscmO91vmOhMujp9r5g+p4WhzE1pLZsenmV7TzPodzS9E629xeqfnOO/qGHnlhXp6+Q9vLz9CefpiYESzLFbljkbmXvpn6Bn8/344HyHZ4ws2K99lsRh9N8l26+s9MM4fK978xPGzKplfU8/byxPq/q/yjej9SvznfM3hDL4z7HyPy2buhWVwsZzEZdHme2Gzt+enjM/T31dm4bvP9PLH32Mdf2uG74eHjPTqOXlL1NPx8rh0fLyynpt8zLzz1cmN9NbJIZMRkxGWps63H4/uOx2AQAAFURKIsAAChKIUQAACiFIAsAlLAELKqAFBHt5ey9XmdHlR773J9zv6+evLyfN1bON7dbnjw3fOMJ6517db5f6mMNrRyr3+Z+18j5Lw2/E88PWHhc5WM9MEwlyXGZYlzwyTc9tPbl8PtviMzX8Sm9p7UdHm+HkO9w96v07PV4Mc75zZ8zw9PXWr09tMdHz0diJOxomt1+SPv+l+U/Qn2fpzN8+b+O/S/zYmIZIsvX5X0svJ897oHznpv+xreHvtnM0fpvnTb9tncOV4dbyrV0vofE0OX9r8lHv59PGtXw63lJzd7Y9T5rrc36S3je83jg6G7o8fj7A7HXAAsogAWwLBAUEWCwAUAEoBCwCCy2WJQLAKgAAoJ7ePqvT0PT3Tnd7hdeXPx5+Zr9G8w+v5ut9FL87hucuzay1KeHS5eZ9Jw/ouGdvd+R+mN/gdLdj4TD7r5GtPH38688fXEmGY8ntDyvpkWZYl8fXwLYKQlVL7+PYPDm5+a7OemLjs65jkyMJlmY97gbJ663d40TX3sa1ulpYx3/nL5FFWWItmNFPSZcOVXpzMUH/AIR0g4G7oNkOQ3T3EtKZXoMsKtRgVWVEMqqU2jRqE5W2wFBp3K68Ae9SamiLF/ANIO/bK7eS1juTVrHcgq5JN13UH5fbac0FMEsOGXxL7sDTc4XJ4qP90+44o9JhTenovVqaaHCQmdJvqKT2T6JpkGg+SFjh6oX4rRdrFufBH+7HqhsX3jHLY7oPrtlYbONBTLutHWO7PtSU61mDsuKKvatF+VXOxFOjsdlmiINB7B9ELQaPmCN2KCZY/8Al6cII8NZ289jIxy286Ds40FNR60W9W7s+yvoIlaKSzFuSJtoHRKffTc7NOHA4Gh3YPohjQ2/5k7yQQ6OkCcII8GtaT7c1pHRwVWtxtWrbyVRvJVGo6NvJN6HBN6fuiCCLwaM7OayApyC7xnnSLG4u/mK1j/T9lrH+i1j/RV3ovc53dlDDZGwL0cPiD2fZYU6Gw4haTouCF7Vh7JyFDxIK0fSbliP3oeejhupZ2TaNxXPcUxCzStRw8F6SxmAxKYA0DqR0dJnnxRsIsKyM8tgY2c6X9FuWJTBVA2NFacXZJ1pNBuN3HqRf79d8xWOaxv6kWjJVbEKG9Fyd5ploN9HbbiMRsaKx2IwKc0hPP8Aif0odindl38FDPMLR3/MP18FO7DPU7LnhVvRNeNkfP8Aou6332M3eyaJJwWlEuwblsOMALR9Fnqdk3iw9S2/3628lDgh/AEMExpKqeqhv5l0VLfVV28vuq7eS1jeS7YxjBBOsdQ6/Ap1ujOKZaEbjciYPyuC0n5sEKCEydGdyNtA7YuTr4oBqk3tQudaPBO63jjsaM1Wi84k7Dj2bpUhSFK4rvH0GwxtjWwXG61C12LsdgmXYNxRuwbgPvtYG/qm+Y6zcj5I4p1gKaI6jBaKw93ApwLXBaS7NAyjatE6ocsFpW1D3sCu1VtCuwITTE4YI9F2W1iKPqWIE+CDcT7W/psbtkEjgq7+aru5qu7mnOJjNfT1Gj7cWJ0zjN/UG8XcOqb5jrAVhWA6x44FTIKBsyUwcqCJCZd3Suy7JG2ovRG0ev3QMjb3Lf6eB8m/rsfW3ZAEHfH6KG/m+yqj8ygfm+yN9cNPNZDqWD/Mfr1H1e/VjM9WEe2XtPC27rccEbwV6hE1wuycjQRKFu5XJ1zcU0wf5en9Fx5HZyWQWfgfvN/XYOKiu3MfqoPJBrj5LSiq2+rieOwcAjmXnZefLEp3Rnsb9lnYcfyn9tvIgrMDqvlfaD+nV5Wo4W+vXN8+GxPNOsNBCbbuTbDkinnpC7eNnNcfA/dM+WPogZB2aoQA2W9lt+8ry2G9p825QnOLjQ49Nvrv2HWgrDA57eLbOXVfMLRxRsIv6zcOtNxXym7YOKGktT0CiEOkEMCsRfSMbFzXd/vQ65/+2f8Aj9k0z1DnALR2NPzY0Z289juiOdLbwh57tg+RyKcII/k7TD2bwiEbOOzhsDz/AH6zu7U0TtfMLkbwphFbqa0RcnoFHmplhupOFGf9zlSp+Ea6N2Cc2eBRsVcKuFWQBd6JoDfVOcXfzKm3oiLlarVau8dj5T2grVarVbyTQa4x/Ta77PZCh1yGwaSh2Ddu6vvbIolTBV6cpjYFx7VBUTsgwjap2B5+KNxWKF3tQcLuGzNikFEIm/NG7DqsjTmFkY2XWGhphXhOsTTKOKNozoOIgoEIkLKgXWHnS4yFPio8KBjesNrK3lsXx1fdspHZdbw25paSsaQURSKRZQLETPhrDFB41eJN4Q6ZzlNR2bkZKt5q+k4EI0ATnRiLNjMLu/Am1RV4KdolSr02GhVtgf3jS6UhzhcAmaSuH0NbWThFDQXcE5pbQ0Fyc0ijDFG43UNbWThVNDWl3BOET57GkeWCfJM0pc7AfwUNBdwT5ZUwzsoLC8AYYUDz3BXtGNDmuJ+Ui4UNEncnNIoaJi9VTFH1LA2lAkLSHpJqM0uTUTCtp4IZLmj5UYFd732e+PbrsxtlAIIlFya3mpjgr6ZKn+8Zosbpm8E0BpLbQKNDJ71W9aQ/vRoe0T0ovWkteTjfRo+2VpbzcDfQMGfqtLWn6qA17h9BgqIjMyaAarnv9ESTsM0Y0mYPNO0TdEJwi2jQ9qelF+9PmtPuaM7OX/ujinTVJsyo/wC46KGf7i0vzXTfR/LJTjPRx86MD7p1hR8k6houRRTbvdGjGneFlYm2OHrQaMlnsZdd9J6glNHNF3gGu6Mppa5zZyKJJO+hriOCcSTvoaSOCcSeNDSWndYnEuO+hri3gUZJzNE3XbOsdzTnF3Ghri3gi4nG00SYywoFhCc4ujOiTAuGFDSRwTiXcaJMHBTZlTin3p3kV8uFDdgbGRnkmtt0mKGCwNOKN7djcj1vEbbDGZV5oHUwoUeI39rBP5o3TZQEaIsWexiNocHbOdvW/VtC89QOqxR8QO7SPkUTYihQLUbxsZhNMLIrOnNG8bHl1u8bWVnwh8QP7WBTvIo+VATr0FijRKLiistgXixP5/Fd4fAnxA69G/By0hqx60BaRHbyp3UYZLHKjf1u4dTk74I9VKFu1KG0VPgd96dZkj5I4pqOHVbqRYn2703Edbi222M+qOC0cBoxzVcquUYPlCcITTO3jgjeOps6o+CuNL7Dmm2hO8k8zWQIt2jc5Nsp3bePV4XGnPq2ntdrhtNMLSWb8E3aHn1MIbZ2DQPA3HYetFzWKcS49VmjgfgMqDeLuFGW0LTsHBG87F6upBsywTui7ZKww4eHONAoOzu2eWwwTZDh+uwOszoyQyWZ2ChYNkH+mz1ONF+wUEaSazd9/NTByx2MW+2297dHOakOacQpmsJUwh8t5wQ0rS7JE1Sy9TVaPmKBnepDWjEprm6RovhOc3Rg3Su0HXEJ2kYwnBOXZG/GgGs0+imq1qc9ujDrpRgh1xCb5k4Jmka8jAUHSMbWzUhzTiE7SsHFVg/ePAJE5rR9h3actqIWdN4Wj6DjyKcI9jTeo60XO2IR2dGZJxyQtTlcNr0Qtb7bD+kM0HCnLZyK0ZBsR0kmexgmxYMU6LWm5TBebCiQ0D5pVauIvzX+3HYRvCLiGT2RimMDNGLXWy4rR2xgp/qEj3T507uNi/6joNjoi9DSF3RiIgNG5MMtTrnDoocXLSFz7LGzdzVUNbMNEyiQHuFifDQBmsyVpGl1lkcExtVshO0YfZig2oMv71msnUH5bUCKSnWsfem2tPZKqlVSiCoNHLZ0glaPpN9Qj8OMFWhowpPUG/3Xyn02ZniixZnaBiiaZ2hZRJ5nak8zSST4A7w9qO8FkaA63KiblpLWOx7qrGre05hVyqxVYrFZUZrPYHRenXZ4H+xDNOEVv5KKKCPh7JZW0ZFYPHqKRgKHq86HnVpCyo7qyoN+w8SForR3Ufg8Btjbm64LFvt4jP8ABR8zLRsTAKayTmm+YzWjtHdyRCFPPZGx2XZhG1vfF3wmdMBCm9C1rqLQ4nnQE68ey5bOfhweaH8FLbnX8aQjcL1ggUeYTHA7jYUcKcQhb1DukFojV3YJwq+yPXhZ0nYNrTYVgbWncsCZ5UOdYy4U4bWW2dyssNiMYqyI9VZWi/BOvnwi7yQuWKN1PJC/5tkX7DuqeL18pu67KyjcsqGjisFMkL5povgq7jRmjiJohZikeIHXptEGht/WFBHbf5HJHyPW5lb6MzRlcjcbtmOky80ZQELHCzypy8SG1GgWddeEUNt3kj1m5fUt1PeX8tQxTnCitCwWQsomac9rMeMZRQO0e1gUb+r3LeVlTkUMrdhuVtHe6v6eoG2LdvCki/wNHwptU7Pzi4o2EdVuX1FZqEaBgr5UUPtOWKa3JCOg3q92xvTm1qsQohrmTGEqrZUJsNhQbUl9WEW1jXIRi1szbKq1i+2cl9X6qpECa/ko6NS0fVQWV65Pl91UrS6IyVWaoaQOKAgO0RMZKpXrNmf2RuKDKnSAVgIbIvmUwAkNkzIcomNHWjNVKtYmRwQbVqujj4blHYHbHqjYR1O9ZGdnNYgq44oXomV3V3kE5scep+kbM9q/eq1wjyR6RqxwRdJFymyZ81WMIOsWJRcYQzlyFhQdegb1PavU4R5IOsQTjIU2KbEZ6OSbIq44qbPD16NiBQseP+XVd4UZ02BHGjAousWJpIDuKiEFiFu2chstINXDGFZ0xIR+ZxB8lWtD4q7k669AhrK0AokNqXlZmFWa6reBgpEi8YhOIFsfdaOyG9IoEOrXHCxB7YZecFWaYtjMZoOF0wqwFa6USGgGJOaJDQDE70fEgKIt6nuU97ZbzUGvQFWKNqI61rYe8QSi01mirO5R2CTzRF5rA71uWkBIBkQo7cR5LeEwGs82/ZW1iI3LuCER0XiCmA1WzxtUf7ij5KqgyBG4ruGfVPBLS6sIwUQ2ZGMIyZutu8XHFHhRi3Y3LFFBFTSEerrthRIcJsUTRF+KvqgHnRCiymQJunFGypf5oWq6qJM0FwbJgbyrBUsJKNlkzhCmsHXEYo/NzUgRmrLbjgpB8VZrnQVgdgI/BYhxsVaBVNb7oRY4zbCwJVYGwRagb9G1REG0pxmHdH7IRBGf6IxW7B3NzTeywVW+Sa4CqIMmIhVq1rbc01wDiBVPutI4P6GfopkG7cMlILgei0mAN6rCuXVrcVrI1bIc6b+CdFRtjQDPmUDLh7I4i7NWNa0GAqobw8V4hfyFNuBTrxt1lNJvKz/A4I3Jtyx2hi0Gk9ZmU0EFjLScUbyJn9l/ksn9LgrwXEX3BEV6ps81FsTvoDbCTdcog/Lbihho/VRaWzONBZXIKiwNrRxQsrtJA3rANNfjCPac2ZyV73T5R4xwQRuTeSOGye7QU3rS6yI4qbFPZuQMTegVPavU2IKbrk604Ke1fRkhfN6m1TaFPavU2LBvjK8ZUNREIGRjsd1G0oddIMXwpFgmMYRiHYTbCGcKRZfuXdE81ZdMYwrADvXdv8043NmAUCM4xXeuWKsJyxtVhi+MFZzTrnWyMQmwGtN6yKm1roj4C81THFPBLa7ZJF1q6Ws9LloxJL3VrJxsT2gaQsJjetMOhVN4iDhCAdozViC3oKXA/Q2tmrZnGwodrTADhmtG3oQMJreaaMG2DNEQa7rEXawEwLOwULXhnRWkkiti2LYOKLSBrb4svTBL64wmxaRsQ0x0YWj/ANs5Z71Vnf3fB5UjgrQpVZXofACbRdFy7zKvoiHVmWbl9UoTbhksRfwFytmIhD5WwrK7oLvK5Wy9sK0WQRHqsiQzzWRTQaxM24Js1tJfOC7zQ1dyfVPkCtMhCysAB5I4mQh19yL3HzVYznNqDiOBRJJzm1F7iN5Vd/5iq7h5lXqTAuGAQe4DKSgSDuKLiTnNqkwb7b0DCL3GN6rv/MVWM8VXdPFSYyWd/hGVKlG38VMBRl8WOvv8YPFZzrb7k1zq2UzHHrnWe6b69a64KOSHW1LeKHW6S2tcMkCa24yr/D+aIqiIAxQvvO3MxtdnahQUdoKdqCohTO0bNqFVKMDaaJQciRswVCy8MdluZWjbJxcb0y3Sn0RtJtJQUolNaCjDVjtXmjGi5VkbeKYPPqAPNGmUXIK4I27V7lfuWNFyLuSv3lNox2T2fdNAaPVYo0VgnGU0CdymNycZPhjNYNsQvTgbTYcKWiVpHVRkL0OaBrOTjOycUyzfipoNEpxmgKPPauFJpvKO05NsRpCCc6dyu2t6aLkSsKMVKcbE3mrzRdsTEz6LWfzktZ6fZawcvstZ/OS1np9lrBy+y1g5fZaz0+y1np9lXmqKSYgSq7uSru5Ku7kq7uSru5Ku7kq7uSruVd3JV3clXdyCru5BV3cgq7uQVd3IKu7kFXdyVd3JV3IOM/wA3f2ZrSd+HNWcFpOi5RYtE2Zvb+ycQ0cymDi5MtOJTz2rpxRQ2bgdg+Qpe6o31TBDW3ZnaCmnFG+gLS+Tdu7YJsyQoNjfdaPC92MqdlomEbEaB5oLFBHpaQ+lA2f8AL4bdT9Pwm4/2YVWQtIZANhTQN5R7TRB4YLuhYLR3lOFmM5rAWN4BXrDZmdvE7d2wKblltZbcIWG7bsHknWhRCyQRouA9Vnt/5dSSApCBClV280Oo3U/TtEQg1x8isVVd+U0EQoM5Raqp5FRao2dx/s2ATOJRvmFmz2QPRFgpFiCqlYu2puwpA2Andbjtld2NorDZyWCCzWY28dn/AC6nV63+mejMLVaiYsmU0at7WghwMWp17tCZVQVXMcSE0QBcOo3U/Ts4TbwCqwWGdH9Q+9qYYLpn1Th0y8A4SP4PVXsrREYQhdP7KY0gaHN3jEL/AMSbpnPIddUqxG9fLqpPHBYO0Yq8T/Bs7j/ZnXOuKYBJxWSBjak89nJELD4w2EIXe/W5oC3rv8uprPa4COiYRfpHB+bpuyTnPeBg50hYsEDK1W1mAgZW9Z9OyyBNl0rudqFWBuRNrbsh5LojgLUVYCwWQrK9WrdgnVYOQXyfwr5B+mzuP8AZnCsE1ob4I3O+G3U/T8JuPi95gCVrFrFrAtYFrAtYFrAtYFrAtYFrAtYtYFrAtYFrAtYFrAtYFrAtYE14JIpJiQtY1axq1jVrGrWNWsatY1axq1jVrGrWNWsatY1axq1jVrGrWNWsatY1VwbD+IX1D3XRJrYBfK2+yEIqOaeAKEVtVNmcp4vst3rHRWv3qLHNKsM1uSgHpQLMtp2Qg5Jpk1uk7cmgRMOF/mqrYtixYlzvllHFvJME9FkQtIILrBN43pjA8ntE2qq3WAGBtHIqAABLiLimwRF18FPYBdFiF4Foy3prWjRhsg5+ластакес…');

// Fetch all bikes with their category IDs for client-side filtering
$featured = $pdo->query("
    SELECT b.*, c.country_code, c.flag_emoji,
           bv.color_name, bv.image_url AS variant_img,
           i.stock_qty, i.stock_status
    FROM bikes b
    JOIN categories    c  ON c.category_id = b.category_id
    JOIN bike_variants bv ON bv.bike_id    = b.bike_id AND bv.is_default = 1
    JOIN inventory     i  ON i.variant_id  = bv.variant_id
    WHERE b.is_featured = 1
    LIMIT 1
")->fetch();

$variants = [];
if ($featured) {
    $stmt = $pdo->prepare("
        SELECT bv.*, i.stock_qty, i.stock_status
        FROM bike_variants bv
        JOIN inventory i ON i.variant_id = bv.variant_id
        WHERE bv.bike_id = ?
        ORDER BY bv.is_default DESC, bv.variant_id ASC
    ");
    $stmt->execute([$featured['bike_id']]);
    $variants = $stmt->fetchAll();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();

// Fetch one representative bike per category for hero switching
$catHeroes = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("
        SELECT b.*, c.country_code, c.flag_emoji,
               bv.color_name, bv.image_url AS variant_img,
               i.stock_qty, i.stock_status
        FROM bikes b
        JOIN categories    c  ON c.category_id = b.category_id
        JOIN bike_variants bv ON bv.bike_id = b.bike_id AND bv.is_default = 1
        JOIN inventory     i  ON i.variant_id = bv.variant_id
        WHERE b.category_id = ?
        ORDER BY b.is_featured DESC, b.bike_id DESC
        LIMIT 1
    ");
    $stmt->execute([$cat['category_id']]);
    $row = $stmt->fetch();
    if ($row) $catHeroes[$cat['category_id']] = $row;
}

// Always fetch ALL bikes — filtering is done client-side
$sql = "
    SELECT b.bike_id, b.bike_name, b.model, b.price, b.image_url, b.is_featured,
           b.category_id,
           c.country_code, c.flag_emoji,
           bv.color_name, bv.image_url AS variant_img,
           i.stock_qty, i.stock_status
    FROM bikes b
    JOIN categories    c  ON c.category_id  = b.category_id
    JOIN bike_variants bv ON bv.bike_id     = b.bike_id AND bv.is_default = 1
    JOIN inventory     i  ON i.variant_id   = bv.variant_id
    ORDER BY b.is_featured DESC, b.bike_id DESC
";
$bikes = $pdo->query($sql)->fetchAll();

function stockBadge(string $status): string {
    $map = ['In Stock' => 'badge-green', 'Low Stock' => 'badge-yellow', 'Out of Stock' => 'badge-red'];
    $cls = $map[$status] ?? 'badge-red';
    return "<span class='badge $cls'>$status</span>";
}

function resolveImg(?string $dbPath, string $fallback): string {
    if ($dbPath && (str_starts_with($dbPath, 'http') || file_exists($dbPath))) {
        return htmlspecialchars($dbPath);
    }
    return $fallback;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bike Concept Vault</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#0a0a0a;--bg2:#111;--bg3:#181818;--bg4:#1e1e1e;
  --red:#e8000d;--red2:#ff1a24;--reddim:rgba(232,0,13,.09);
  --border:#1f0002;--border2:#3d0005;--border3:#2a0003;
  --text:#f0f0f0;--textd:#8a8a8a;--textdd:#3a3a3a;
  --radius:8px;--card-radius:10px;
}
body{background:var(--bg);color:var(--text);font-family:'Rajdhani',sans-serif;min-height:100vh;overflow-x:hidden;}

/* TOPBAR */
.topbar{
  background:rgba(10,10,10,.96);border-bottom:1px solid #1a0002;
  padding:0 28px;display:flex;align-items:center;justify-content:space-between;
  height:62px;position:sticky;top:0;z-index:100;backdrop-filter:blur(10px);
}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none;font-family:'Orbitron',monospace;font-size:11px;font-weight:900;color:var(--text);letter-spacing:1px;line-height:1.15;}
.logo-icon{width:34px;height:34px;background:var(--red);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.logo-icon svg{width:20px;height:20px;fill:none;stroke:#fff;stroke-width:1.5;}
.logo-text .top{color:var(--red2);font-size:12px;display:block;}
.logo-text .bot{color:var(--textd);font-size:9px;letter-spacing:3px;display:block;}

.cat-nav{display:flex;align-items:center;gap:2px;margin:0 16px;}
.cat-nav a{
  display:flex;align-items:center;gap:5px;padding:6px 13px;border-radius:6px;
  font-size:11px;font-weight:700;letter-spacing:.8px;text-decoration:none;
  color:var(--textd);transition:all .18s;border:1px solid transparent;text-transform:uppercase;
  cursor:pointer;
}
.cat-nav a .flag{font-size:13px;}
.cat-nav a:hover{color:var(--text);background:var(--bg3);border-color:var(--border3);}
.cat-nav a.active{color:var(--red2);background:var(--reddim);border-color:var(--border2);}

/* TOPBAR RIGHT */
.topbar-right{display:flex;align-items:center;gap:8px;}
.search-box{background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:6px 12px;display:flex;align-items:center;gap:7px;width:210px;transition:border-color .18s;}
.search-box:focus-within{border-color:var(--border3);}
.search-box input{background:none;border:none;outline:none;color:var(--text);font-family:'Rajdhani',sans-serif;font-size:13px;width:100%;}
.search-box input::placeholder{color:var(--textdd);}
.search-clear{background:none;border:none;color:var(--textd);cursor:pointer;font-size:14px;padding:0;line-height:1;display:none;transition:color .15s;}
.search-clear:hover{color:var(--red2);}
.search-clear.visible{display:block;}

/* HERO */
.hero{
  position:relative;background:var(--bg2);min-height:500px;
  display:flex;border-bottom:1px solid var(--border2);overflow:hidden;
}
.hero::before{
  content:'';position:absolute;inset:0;pointer-events:none;
  background-image:repeating-linear-gradient(-45deg,transparent,transparent 55px,rgba(232,0,13,.022) 55px,rgba(232,0,13,.022) 56px);
}
.hero-top-line{position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--red) 0%,rgba(232,0,13,.3) 55%,transparent 100%);z-index:10;}

.hero-left{flex:1;position:relative;display:flex;align-items:center;justify-content:center;padding:30px 30px 130px 50px;overflow:hidden;}

.hero-wm{position:absolute;bottom:115px;left:44px;z-index:2;pointer-events:none;line-height:1;}
.hero-wm-brand{font-family:'Orbitron',monospace;font-size:96px;font-weight:900;font-style:italic;letter-spacing:-3px;line-height:.9;}
.hero-wm-brand .part-red{color:var(--red);text-shadow:0 0 60px rgba(232,0,13,.3);}
.hero-wm-brand .part-white{color:#fff;}
.hero-wm-sub{font-size:15px;letter-spacing:9px;color:var(--textd);margin-top:8px;text-transform:uppercase;font-style:normal;}

.hero-bike-img{
  position:relative;z-index:3;
  max-width:640px;max-height:380px;width:100%;
  object-fit:contain;
  filter:drop-shadow(0 18px 55px rgba(232,0,13,.18)) drop-shadow(0 6px 18px rgba(0,0,0,.65));
  transform:scale(1.06);
  transition:opacity .3s ease;
}

/* Variant bar — pinned to bottom, always above everything */
.variants-bar{
  position:absolute;bottom:0;left:0;right:0;z-index:20;
  display:flex;flex-direction:column;gap:6px;
  padding:10px 44px 14px;
  background:linear-gradient(to top,rgba(0,0,0,.72) 60%,transparent 100%);
  pointer-events:auto;
}
.var-choose{font-size:10px;font-weight:700;letter-spacing:2px;color:var(--textd);text-transform:uppercase;}
.variants-bottom{display:flex;gap:10px;flex-wrap:wrap;}
.var-item{display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;}
.var-thumb{
  width:58px;height:50px;border-radius:6px;
  background:var(--bg3);border:2px solid var(--border2);
  overflow:hidden;display:flex;align-items:center;justify-content:center;
  transition:border-color .18s;
}
.var-thumb img{width:100%;height:100%;object-fit:cover;}
.var-swatch{width:100%;height:100%;}
.var-item.active .var-thumb,.var-item:hover .var-thumb{border-color:var(--red2);}
.var-name{font-size:8.5px;font-weight:700;color:var(--textd);letter-spacing:.4px;text-transform:uppercase;text-align:center;max-width:60px;line-height:1.2;}

.hero-right{
  width:290px;flex-shrink:0;background:rgba(8,8,8,.72);
  border-left:1px solid var(--border2);padding:32px 24px;
  display:flex;flex-direction:column;justify-content:flex-end;
  position:relative;z-index:5;backdrop-filter:blur(8px);
}
.hero-price{font-family:'Orbitron',monospace;font-size:38px;font-weight:900;color:var(--red2);margin-bottom:3px;letter-spacing:-1px;}
.hero-model{font-family:'Orbitron',monospace;font-size:13px;font-weight:700;color:var(--text);letter-spacing:.8px;margin-bottom:14px;}

.stock-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-bottom:14px;width:fit-content;}
.stock-pill.green{background:var(--red);color:#fff;}
.stock-pill.yellow{background:rgba(234,179,8,.15);color:#eab308;border:1px solid rgba(234,179,8,.3);}
.stock-pill.red-pill{background:rgba(239,68,68,.12);color:#ef4444;border:1px solid rgba(239,68,68,.2);}
.stock-dot{width:6px;height:6px;border-radius:50%;background:currentColor;}

.hero-desc{font-size:12.5px;color:var(--textd);line-height:1.85;margin-bottom:22px;}

.hero-btns{display:flex;gap:8px;}
.btn-hero-edit{
  flex:1;background:#fff;color:#111;border:none;border-radius:6px;
  padding:12px 14px;font-family:'Orbitron',monospace;font-size:11px;font-weight:700;
  letter-spacing:1px;cursor:pointer;text-decoration:none;
  display:flex;align-items:center;justify-content:center;gap:6px;transition:background .18s;
}
.btn-hero-edit:hover{background:#e8e8e8;}
.btn-hero-add{
  flex:1;background:var(--bg3);color:var(--text);border:1px solid var(--border2);border-radius:6px;
  padding:12px 14px;font-family:'Orbitron',monospace;font-size:11px;font-weight:700;
  letter-spacing:1px;cursor:pointer;text-decoration:none;
  display:flex;align-items:center;justify-content:center;gap:6px;transition:all .18s;
}
.btn-hero-add:hover{border-color:var(--red);color:var(--red2);}

.sparkle{position:absolute;bottom:22px;right:22px;font-size:26px;color:rgba(255,255,255,.12);z-index:4;}

/* INVENTORY */
.page-content{padding:26px 28px;}
.section-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:15px;}
.section-hdr h2{font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:2px;color:var(--textd);}
.section-hdr span{font-size:12px;color:var(--textd);}
.bike-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(196px,1fr));gap:13px;}
.bike-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--card-radius);overflow:hidden;transition:all .22s;cursor:pointer;}
.bike-card:hover{border-color:var(--border2);transform:translateY(-3px);}
.bike-card.feat{border-color:var(--red);}
.bike-card.previewing{border-color:var(--red2);box-shadow:0 0 0 2px rgba(255,26,36,.25);transform:translateY(-3px);}
.card-img{height:126px;background:var(--bg3);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
.card-img img{max-width:90%;max-height:106px;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(232,0,13,.1));}
.card-img .no-img{font-size:11px;color:var(--textdd);}
.card-country{position:absolute;top:7px;right:7px;font-size:9px;font-weight:700;letter-spacing:1px;background:var(--reddim);color:var(--red2);border:1px solid var(--border2);border-radius:3px;padding:2px 7px;}
.card-body{padding:11px 13px;}
.card-name{font-family:'Orbitron',monospace;font-size:11px;font-weight:700;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.card-model{font-size:11px;color:var(--textd);margin-bottom:7px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.card-row{display:flex;align-items:center;justify-content:space-between;}
.card-price{font-family:'Orbitron',monospace;font-size:13px;color:var(--red2);}
.card-actions{display:flex;gap:6px;margin-top:9px;}
.card-btn{flex:1;text-align:center;padding:5px 0;border-radius:4px;font-size:10px;font-weight:700;letter-spacing:.5px;cursor:pointer;transition:all .18s;text-decoration:none;}
.card-btn.edit{background:var(--bg3);border:1px solid var(--border2);color:var(--text);}
.card-btn.edit:hover{border-color:var(--red);color:var(--red2);}
.card-btn.del{background:none;border:1px solid var(--border);color:var(--textd);}
.card-btn.del:hover{border-color:#7f1d1d;color:#f87171;}

/* BADGES */
.badge{display:inline-block;font-size:10px;font-weight:700;letter-spacing:.5px;padding:3px 10px;border-radius:12px;}
.badge-green{background:rgba(34,197,94,.12);color:#22c55e;border:1px solid rgba(34,197,94,.2);}
.badge-yellow{background:rgba(234,179,8,.12);color:#eab308;border:1px solid rgba(234,179,8,.2);}
.badge-red{background:rgba(239,68,68,.12);color:#ef4444;border:1px solid rgba(239,68,68,.2);}

/* DELETE MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:200;align-items:center;justify-content:center;}
.modal-overlay.show{display:flex;}
.modal{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--card-radius);padding:30px;max-width:380px;width:90%;text-align:center;position:relative;}
.modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--red);border-radius:var(--card-radius) var(--card-radius) 0 0;}
.modal-icon{font-size:36px;margin-bottom:12px;}
.modal h3{font-family:'Orbitron',monospace;font-size:16px;font-weight:700;margin-bottom:8px;}
.modal p{font-size:13px;color:var(--textd);line-height:1.6;margin-bottom:22px;}
.modal-name{color:var(--red2);font-weight:700;}
.modal-btns{display:flex;gap:10px;justify-content:center;}
.btn-cancel{background:var(--bg3);border:1px solid var(--border2);color:var(--text);padding:9px 24px;border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:600;cursor:pointer;}
.btn-cancel:hover{background:var(--bg4);}
.btn-confirm-del{background:var(--red);border:none;color:#fff;padding:9px 24px;border-radius:6px;font-family:'Orbitron',monospace;font-size:10px;font-weight:700;letter-spacing:1px;cursor:pointer;}
.btn-confirm-del:hover{background:var(--red2);}

/* EMPTY + TOAST */
.empty{text-align:center;padding:60px 20px;color:var(--textd);}
.empty-icon{font-size:40px;margin-bottom:12px;opacity:.4;}
.empty h3{font-family:'Orbitron',monospace;font-size:14px;margin-bottom:8px;color:var(--textdd);}
.empty p{font-size:13px;}
.toast{position:fixed;bottom:24px;right:24px;background:var(--bg2);border:1px solid var(--border2);border-left:3px solid #22c55e;border-radius:6px;padding:12px 18px;font-size:13px;font-weight:600;z-index:300;transition:all .3s;}
.toast.hide{transform:translateY(20px);opacity:0;}
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <a href="index.php" class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 22 22"><polygon points="11,2 20,8 20,14 11,20 2,14 2,8"/><polyline points="11,2 11,20"/><line x1="2" y1="8" x2="20" y2="8"/><line x1="2" y1="14" x2="20" y2="14"/></svg>
    </div>
    <div class="logo-text">
      <span class="top">BIKE CONCEPT</span>
      <span class="bot">VAULT</span>
    </div>
  </a>

  <!-- Category nav — client-side filtering, no page reload -->
  <nav class="cat-nav">
    <a href="#" class="active" data-cat="0" onclick="setCat(0,this);return false;">ALL</a>
    <?php foreach ($categories as $cat): ?>
    <a href="#" data-cat="<?= $cat['category_id'] ?>"
       onclick="setCat(<?= $cat['category_id'] ?>,this);return false;">
      <span class="flag"><?= $cat['flag_emoji'] ?></span>
      <?= htmlspecialchars($cat['country_code']) ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <div class="topbar-right">
    <div class="search-box">
      <span style="color:var(--textd);font-size:14px;">&#128269;</span>
      <input type="text" id="liveSearch" placeholder="Search bikes..." autocomplete="off"
             oninput="applyFilters()">
      <button class="search-clear" id="searchClear"
              onclick="clearSearch()" title="Clear">&#10005;</button>
    </div>
  </div>
</header>

<!-- TOASTS -->
<?php if (isset($_GET['deleted'])): ?>
<div class="toast" id="toast">&#10003; Bike deleted</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php elseif (isset($_GET['created'])): ?>
<div class="toast" id="toast">&#10003; Bike added</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php elseif (isset($_GET['updated'])): ?>
<div class="toast" id="toast">&#10003; Bike updated</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php endif; ?>

<!-- HERO -->
<?php if ($featured): ?>
<?php
  $heroImg = resolveImg($featured['variant_img'] ?: $featured['image_url'], FALLBACK_BIKE_IMG);
  $bikeName = htmlspecialchars($featured['bike_name']);
  $wmRed   = mb_substr($featured['bike_name'], 0, 3);
  $wmWhite = mb_substr($featured['bike_name'], 3);
  $sq = $featured['stock_status'];
  $pillCls = $sq === 'In Stock' ? 'green' : ($sq === 'Low Stock' ? 'yellow' : 'red-pill');
?>
<section class="hero">
  <div class="hero-top-line"></div>

  <div class="hero-left">
    <div class="hero-wm">
      <div class="hero-wm-brand">
        <span class="part-red"><?= htmlspecialchars($wmRed) ?></span><span class="part-white"><?= htmlspecialchars($wmWhite) ?></span>
      </div>
      <div class="hero-wm-sub">Scooter Edition</div>
    </div>

    <img class="hero-bike-img"
         id="heroBikeImg"
         src="<?= $heroImg ?>"
         alt="<?= $bikeName ?>"
         onerror="this.src='<?= FALLBACK_BIKE_IMG ?>'" >

    <?php if ($variants): ?>
    <div class="variants-bar" id="variantsBar">
      <div class="var-choose" id="varChooseLabel">Choose your <?= $bikeName ?> :</div>
      <div class="variants-bottom" id="variantsRow">
        <?php foreach ($variants as $v): ?>
        <?php $vImg = resolveImg($v['image_url'] ?? null, ''); ?>
        <div class="var-item <?= $v['is_default'] ? 'active' : '' ?>"
             onclick="selectVariant(this,'<?= htmlspecialchars(addslashes($v['image_url'] ?? '')) ?>')">
          <div class="var-thumb">
            <?php if ($vImg): ?>
              <img src="<?= $vImg ?>" alt="<?= htmlspecialchars($v['color_name']) ?>" onerror="this.style.display='none'">
            <?php else: ?>
              <div class="var-swatch" style="background:#555"></div>
            <?php endif; ?>
          </div>
          <div class="var-name"><?= htmlspecialchars($v['color_name']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="hero-right">
    <div class="hero-price" id="heroPrice">&#8369;<?= number_format($featured['price'], 0) ?></div>
    <div class="hero-model" id="heroModel"><?= $bikeName ?> <?= htmlspecialchars($featured['model']) ?></div>

    <div class="stock-pill <?= $pillCls ?>" id="heroPill">
      <span class="stock-dot"></span>
      <?= htmlspecialchars($sq) ?>
    </div>

    <div class="hero-desc" id="heroDesc"><?= nl2br(htmlspecialchars($featured['description'] ?? 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.')) ?></div>

    <div class="hero-btns">
      <a href="edit.php?id=<?= $featured['bike_id'] ?>" class="btn-hero-edit" id="heroEditBtn">EDIT</a>
      <a href="create.php" class="btn-hero-add">ADD NEW</a>
    </div>
  </div>

  <div class="sparkle">✦</div>
</section>
<?php endif; ?>

<!-- INVENTORY GRID -->
<div class="page-content">
  <div class="section-hdr">
    <h2 id="sectionLabel">INVENTORY</h2>
    <span id="bikeCount"><?= count($bikes) ?> bike<?= count($bikes) !== 1 ? 's' : '' ?> found</span>
  </div>

  <?php if (empty($bikes)): ?>
  <div class="empty">
    <div class="empty-icon">&#128663;</div>
    <h3>No bikes found</h3>
    <p>Add your first bike to get started</p>
  </div>
  <?php else: ?>
  <div class="bike-grid" id="bikeGrid">
    <?php foreach ($bikes as $b): ?>
    <div class="bike-card <?= $b['is_featured'] ? 'feat' : '' ?>"
         data-search="<?= strtolower(htmlspecialchars($b['bike_name'].' '.$b['model'])) ?>"
         data-cat="<?= (int)$b['category_id'] ?>"
         data-bike-id="<?= $b['bike_id'] ?>"
         data-bike-name="<?= htmlspecialchars(addslashes($b['bike_name'])) ?>"
         data-model="<?= htmlspecialchars(addslashes($b['model'])) ?>"
         data-price="<?= $b['price'] ?>"
         data-stock="<?= htmlspecialchars($b['stock_status']) ?>"
         data-img="<?= resolveImg($b['variant_img'] ?: $b['image_url'], '') ?>"
         data-desc="<?= htmlspecialchars(addslashes($b['description'] ?? '')) ?>"
         onclick="previewBike(this)">
      <div class="card-img">
        <?php $cimg = resolveImg($b['variant_img'] ?: $b['image_url'], FALLBACK_BIKE_IMG); ?>
        <img src="<?= $cimg ?>" alt="<?= htmlspecialchars($b['bike_name']) ?>"
             onerror="this.src='<?= FALLBACK_BIKE_IMG ?>'">
        <span class="card-country"><?= htmlspecialchars($b['flag_emoji']) ?> <?= htmlspecialchars($b['country_code']) ?></span>
      </div>
      <div class="card-body">
        <div class="card-name"><?= htmlspecialchars($b['bike_name']) ?></div>
        <div class="card-model"><?= htmlspecialchars($b['model']) ?></div>
        <div class="card-row">
          <span class="card-price">&#8369;<?= number_format($b['price'], 0) ?></span>
          <?= stockBadge($b['stock_status']) ?>
        </div>
        <div class="card-actions">
          <a href="edit.php?id=<?= $b['bike_id'] ?>" class="card-btn edit" onclick="event.stopPropagation()">&#9998; EDIT</a>
          <button class="card-btn del" onclick="event.stopPropagation();openDelete(<?= $b['bike_id'] ?>, '<?= addslashes($b['bike_name']) ?> <?= addslashes($b['model']) ?>')">&#128465; DEL</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Dynamic empty state (shown by JS when nothing matches) -->
  <div class="empty" id="liveEmpty" style="display:none;">
    <div class="empty-icon">&#128663;</div>
    <h3>No bikes found</h3>
    <p id="liveEmptyMsg"></p>
  </div>
  <?php endif; ?>
</div>

<!-- DELETE MODAL -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-icon">&#9888;</div>
    <h3>CONFIRM DELETE</h3>
    <p>Are you sure you want to delete<br><span class="modal-name" id="deleteName"></span>?<br><br>This will remove all variants and inventory records. This action cannot be undone.</p>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="closeDelete()">Cancel</button>
      <a href="#" id="deleteConfirmBtn" class="btn-confirm-del">DELETE</a>
    </div>
  </div>
</div>

<script>
/* ── HERO DATA per category (from PHP) ── */
const FALLBACK_IMG = <?= json_encode(FALLBACK_BIKE_IMG) ?>;

const CAT_HEROES = {
  0: <?php
    if ($featured) {
      echo json_encode([
        'bike_id'      => $featured['bike_id'],
        'bike_name'    => $featured['bike_name'],
        'model'        => $featured['model'],
        'price'        => $featured['price'],
        'stock_status' => $featured['stock_status'],
        'img'          => $featured['variant_img'] ?: ($featured['image_url'] ?? ''),
        'description'  => $featured['description'] ?? '',
      ]);
    } else {
      echo 'null';
    }
  ?>
  <?php foreach ($catHeroes as $catId => $hero): ?>,
  <?= (int)$catId ?>: <?= json_encode([
    'bike_id'      => $hero['bike_id'],
    'bike_name'    => $hero['bike_name'],
    'model'        => $hero['model'],
    'price'        => $hero['price'],
    'stock_status' => $hero['stock_status'],
    'img'          => $hero['variant_img'] ?: ($hero['image_url'] ?? ''),
    'description'  => $hero['description'] ?? '',
  ]) ?>
  <?php endforeach ?>
};

const CAT_LABELS = {
  0: 'INVENTORY'
  <?php foreach ($categories as $cat): ?>,
  <?= $cat['category_id'] ?>: 'INVENTORY — <?= addslashes($cat['country_code']) ?>'
  <?php endforeach; ?>
};

/* ── STATE ── */
let activeCat  = 0;
let searchTerm = '';

const allCards  = Array.from(document.querySelectorAll('.bike-card'));
const countEl   = document.getElementById('bikeCount');
const labelEl   = document.getElementById('sectionLabel');
const gridEl    = document.getElementById('bikeGrid');
const emptyEl   = document.getElementById('liveEmpty');
const emptyMsg  = document.getElementById('liveEmptyMsg');
const clearBtn  = document.getElementById('searchClear');

/* ── HERO ELEMENTS ── */
const heroBikeImg   = document.getElementById('heroBikeImg');
const heroPrice     = document.getElementById('heroPrice');
const heroModel     = document.getElementById('heroModel');
const heroDesc      = document.getElementById('heroDesc');
const heroPill      = document.getElementById('heroPill');
const heroEditBtn   = document.getElementById('heroEditBtn');
const heroWmRed     = document.querySelector('.hero-wm-brand .part-red');
const heroWmWhite   = document.querySelector('.hero-wm-brand .part-white');
const varChooseLabel = document.getElementById('varChooseLabel');

/* ── UPDATE HERO ── */
function updateHero(catId) {
  const data = CAT_HEROES[catId] || CAT_HEROES[0];
  if (!data) return;

  const img = (data.img && data.img.trim()) ? data.img : FALLBACK_IMG;

  if (heroBikeImg) {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = img;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 180);
  }

  if (heroPrice)  heroPrice.textContent  = '₱' + Number(data.price).toLocaleString();
  if (heroModel)  heroModel.textContent  = data.bike_name + ' ' + data.model;
  if (heroDesc)   heroDesc.textContent   = data.description || 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.';
  if (heroEditBtn) heroEditBtn.href      = 'edit.php?id=' + data.bike_id;

  if (varChooseLabel) varChooseLabel.textContent = 'Choose your ' + data.bike_name + ' :';

  /* Watermark */
  if (heroWmRed)   heroWmRed.textContent   = data.bike_name.substring(0, 3);
  if (heroWmWhite) heroWmWhite.textContent = data.bike_name.substring(3);

  /* Stock pill */
  if (heroPill) {
    const s = data.stock_status;
    heroPill.className = 'stock-pill ' + (s === 'In Stock' ? 'green' : s === 'Low Stock' ? 'yellow' : 'red-pill');
    heroPill.innerHTML = '<span class="stock-dot"></span>' + s;
  }
}

/* ── CATEGORY CLICK ── */
function setCat(catId, linkEl) {
  activeCat = catId;

  document.querySelectorAll('.cat-nav a[data-cat]').forEach(a => {
    a.classList.toggle('active', parseInt(a.dataset.cat) === catId);
  });

  if (labelEl) labelEl.textContent = CAT_LABELS[catId] || 'INVENTORY';

  updateHero(catId);
  applyFilters();
}

/* ── SEARCH INPUT ── */
document.getElementById('liveSearch').addEventListener('input', function() {
  searchTerm = this.value.trim().toLowerCase();
  clearBtn.classList.toggle('visible', searchTerm.length > 0);
  applyFilters();
});

function clearSearch() {
  const input = document.getElementById('liveSearch');
  input.value = '';
  searchTerm = '';
  clearBtn.classList.remove('visible');
  applyFilters();
  input.focus();
}

/* ── COMBINED FILTER ── */
function applyFilters() {
  let visible = 0;

  allCards.forEach(card => {
    const catMatch    = activeCat === 0 || parseInt(card.dataset.cat) === activeCat;
    const searchMatch = searchTerm === '' || (card.dataset.search || '').includes(searchTerm);
    const show        = catMatch && searchMatch;

    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  if (countEl) countEl.textContent = visible + ' bike' + (visible !== 1 ? 's' : '') + ' found';

  if (gridEl)  gridEl.style.display  = visible === 0 ? 'none' : '';
  if (emptyEl) {
    emptyEl.style.display = visible === 0 ? 'block' : 'none';
    if (emptyMsg) {
      if (searchTerm) {
        emptyMsg.textContent = 'No results for "' + document.getElementById('liveSearch').value.trim() + '"';
      } else if (activeCat !== 0) {
        emptyMsg.textContent = 'No bikes in this category yet.';
      } else {
        emptyMsg.textContent = 'Add your first bike to get started.';
      }
    }
  }
}

/* ── DELETE MODAL ── */
function openDelete(id, name) {
  document.getElementById('deleteName').textContent = name;
  document.getElementById('deleteConfirmBtn').href = 'delete.php?id=' + id;
  document.getElementById('deleteModal').classList.add('show');
}
function closeDelete() {
  document.getElementById('deleteModal').classList.remove('show');
}
document.getElementById('deleteModal').addEventListener('click', function(e){ if(e.target===this) closeDelete(); });

/* ── CARD PREVIEW → HERO ── */
function previewBike(card) {
  const d = card.dataset;
  const img = (d.img && d.img.trim()) ? d.img : FALLBACK_IMG;

  // Highlight selected card
  document.querySelectorAll('.bike-card').forEach(c => c.classList.remove('previewing'));
  card.classList.add('previewing');

  // Fade image swap
  if (heroBikeImg) {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = img;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 150);
  }

  if (heroPrice)   heroPrice.textContent  = '\u20B1' + Number(d.price).toLocaleString();
  if (heroModel)   heroModel.textContent  = d.bikeName + ' ' + d.model;
  if (heroDesc)    heroDesc.textContent   = d.desc || 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.';
  if (heroEditBtn) heroEditBtn.href       = 'edit.php?id=' + d.bikeId;
  if (varChooseLabel) varChooseLabel.textContent = 'Viewing: ' + d.bikeName;

  if (heroWmRed)   heroWmRed.textContent   = d.bikeName.substring(0, 3);
  if (heroWmWhite) heroWmWhite.textContent = d.bikeName.substring(3);

  if (heroPill) {
    const s = d.stock;
    heroPill.className = 'stock-pill ' + (s === 'In Stock' ? 'green' : s === 'Low Stock' ? 'yellow' : 'red-pill');
    heroPill.innerHTML = '<span class=\'stock-dot\'></span>' + s;
  }

  // Scroll hero into view smoothly
  document.querySelector('.hero').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── VARIANT SWITCHER ── */
function selectVariant(el, imgSrc) {
  const row = el.closest('.variants-bottom');
  if (row) row.querySelectorAll('.var-item').forEach(v => v.classList.remove('active'));
  el.classList.add('active');
  if (heroBikeImg && imgSrc && imgSrc.trim() !== '') {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = imgSrc;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 150);
  }
}
</script>
</body>
</html>