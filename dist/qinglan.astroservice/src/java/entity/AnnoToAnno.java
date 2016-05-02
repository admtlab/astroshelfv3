/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_to_anno")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoToAnno.findAll", query = "SELECT a FROM AnnoToAnno a"),
    @NamedQuery(name = "AnnoToAnno.findByAnnoToAnnoId", query = "SELECT a FROM AnnoToAnno a WHERE a.annoToAnnoId = :annoToAnnoId")})
public class AnnoToAnno implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_to_anno_id")
    private Long annoToAnnoId;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @OneToOne(optional = false)
    private Annotation annoSrcId;
    @JoinColumn(name = "anno_tar_id", referencedColumnName = "anno_id")
    @ManyToOne(optional = false)
    private Annotation annoTarId;

    public AnnoToAnno() {
    }

    public AnnoToAnno(Long annoToAnnoId) {
        this.annoToAnnoId = annoToAnnoId;
    }

    public Long getAnnoToAnnoId() {
        return annoToAnnoId;
    }

    public void setAnnoToAnnoId(Long annoToAnnoId) {
        this.annoToAnnoId = annoToAnnoId;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    public Annotation getAnnoTarId() {
        return annoTarId;
    }

    public void setAnnoTarId(Annotation annoTarId) {
        this.annoTarId = annoTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoToAnnoId != null ? annoToAnnoId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoToAnno)) {
            return false;
        }
        AnnoToAnno other = (AnnoToAnno) object;
        if ((this.annoToAnnoId == null && other.annoToAnnoId != null) || (this.annoToAnnoId != null && !this.annoToAnnoId.equals(other.annoToAnnoId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoToAnno[ annoToAnnoId=" + annoToAnnoId + " ]";
    }
    
}
