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
@Table(name = "anno_to_set")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoToSet.findAll", query = "SELECT a FROM AnnoToSet a"),
    @NamedQuery(name = "AnnoToSet.findByAnnoToSetId", query = "SELECT a FROM AnnoToSet a WHERE a.annoToSetId = :annoToSetId")})
public class AnnoToSet implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_to_set_id")
    private Long annoToSetId;
    @JoinColumn(name = "set_tar_id", referencedColumnName = "set_id")
    @ManyToOne(optional = false)
    private SetInfo setTarId;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @OneToOne(optional = false)
    private Annotation annoSrcId;

    public AnnoToSet() {
    }

    public AnnoToSet(Long annoToSetId) {
        this.annoToSetId = annoToSetId;
    }

    public Long getAnnoToSetId() {
        return annoToSetId;
    }

    public void setAnnoToSetId(Long annoToSetId) {
        this.annoToSetId = annoToSetId;
    }

    public SetInfo getSetTarId() {
        return setTarId;
    }

    public void setSetTarId(SetInfo setTarId) {
        this.setTarId = setTarId;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoToSetId != null ? annoToSetId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoToSet)) {
            return false;
        }
        AnnoToSet other = (AnnoToSet) object;
        if ((this.annoToSetId == null && other.annoToSetId != null) || (this.annoToSetId != null && !this.annoToSetId.equals(other.annoToSetId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoToSet[ annoToSetId=" + annoToSetId + " ]";
    }
    
}
